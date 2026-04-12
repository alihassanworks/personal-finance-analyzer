<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TransactionCsvImportController extends Controller
{
    public function create(): View
    {
        return view('transactions.import');
    }

    /**
     * CSV columns: date,type,category,amount,notes (header row required).
     * type: income or expense. category: matches your category name (case-insensitive).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $user = $request->user();
        $path = $request->file('file')->getRealPath();
        if ($path === false) {
            return back()->withErrors(['file' => 'Could not read the uploaded file.']);
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->withErrors(['file' => 'Could not open the uploaded file.']);
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);

            return back()->withErrors(['file' => 'The CSV is empty.']);
        }

        $header = array_map(fn ($h) => Str::lower(trim((string) $h)), $header);
        $map = array_flip($header);
        foreach (['date', 'type', 'category', 'amount'] as $col) {
            if (! isset($map[$col])) {
                fclose($handle);

                return back()->withErrors(['file' => 'CSV must include columns: date, type, category, amount (optional: notes).']);
            }
        }

        $categoriesByName = $user->categories()
            ->get()
            ->keyBy(fn (Category $c) => Str::lower($c->name));

        $imported = 0;
        $errors = [];
        $line = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                if (count(array_filter($row, fn ($c) => $c !== null && $c !== '')) === 0) {
                    continue;
                }

                $date = trim((string) ($row[$map['date']] ?? ''));
                $type = Str::lower(trim((string) ($row[$map['type']] ?? '')));
                $catName = trim((string) ($row[$map['category']] ?? ''));
                $amountRaw = trim((string) ($row[$map['amount']] ?? ''));
                $notes = isset($map['notes']) ? trim((string) ($row[$map['notes']] ?? '')) : null;

                if ($date === '' || ! in_array($type, ['income', 'expense'], true) || $catName === '' || $amountRaw === '') {
                    $errors[] = "Line {$line}: missing required values.";

                    continue;
                }

                $amount = (float) str_replace([',', ' '], '', $amountRaw);
                if ($amount <= 0) {
                    $errors[] = "Line {$line}: amount must be positive.";

                    continue;
                }

                $catKey = Str::lower($catName);
                $category = $categoriesByName->get($catKey);
                if (! $category) {
                    $errors[] = "Line {$line}: unknown category \"{$catName}\".";

                    continue;
                }

                if ($category->type !== $type) {
                    $errors[] = "Line {$line}: type does not match category \"{$category->name}\".";

                    continue;
                }

                Transaction::query()->create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'type' => $type,
                    'amount' => $amount,
                    'transaction_date' => $date,
                    'notes' => $notes ?: null,
                ]);
                $imported++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);

            return back()->withErrors(['file' => 'Import failed: '.$e->getMessage()]);
        }

        fclose($handle);

        $msg = "Imported {$imported} transaction(s).";
        if (count($errors)) {
            $msg .= ' Skipped '.count($errors).' row(s).';
        }

        return redirect()
            ->route('transactions.index')
            ->with('status', $msg)
            ->with('import_errors', array_slice($errors, 0, 20));
    }
}
