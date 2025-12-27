<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountController extends Controller
{
    // Hesap ekleme sayfası
    public function create()
    {
        return view('accounts.create');
    }

    // Hesap düzenleme sayfası
    public function edit($id)
    {
        $account = DB::table('accounts')->where('id', $id)->first();
        
        if (!$account) {
            return redirect()->route('accounts')->with('error', 'Hesap bulunamadı!');
        }
        
        return view('accounts.edit', compact('account'));
    }

    // Hesap ekleme işlemi
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',

            'balance' => 'required|numeric|min:0',
          
        ]);

        $data = $request->except('_token');
        
        // IBAN boşsa veya sütun yoksa IBAN'ı çıkar
        if (empty($data['iban']) || !Schema::hasColumn('accounts', 'iban')) {
            unset($data['iban']);
        }
        
        $insert = DB::table('accounts')->insertGetId($data);

        if ($insert) {
            return redirect()->route('accounts')->with('success', 'Hesap başarıyla eklendi');
        } else {
            return redirect()->back()->with('error', 'Hesap eklenemedi')->withInput();
        }
    }

    // Eski add metodu (geriye dönük uyumluluk için)
    public function add(Request $request)
    {
        return $this->store($request);
    }

    // Hesap güncelleme işlemi
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'description' => 'required|string',
            'balance' => 'required|numeric|min:0',
            'iban' => 'nullable|string|max:34'
        ]);

        $data = $request->except(['_token', '_method', 'id']);
        
        // IBAN boşsa veya sütun yoksa IBAN'ı çıkar
        if (empty($data['iban']) || !Schema::hasColumn('accounts', 'iban')) {
            unset($data['iban']);
        }
        
        $update = DB::table('accounts')->where('id', $id)->update($data);

        if ($update) {
            return redirect()->route('accounts')->with('success', 'Hesap başarıyla güncellendi');
        } else {
            return redirect()->back()->with('error', 'Hesap güncellenemedi')->withInput();
        }
    }

    // Hesap silme işlemi
    public function destroy(Request $request, $id)
    {
        $delete = DB::table('accounts')->where('id', $id)->delete();

        if ($request->ajax()) {
            if ($delete) {
                return response()->json(['success' => true, 'message' => 'Hesap başarıyla silindi']);
            } else {
                return response()->json(['success' => false, 'message' => 'Hesap silinemedi'], 500);
            }
        }

        if ($delete) {
            return redirect()->route('accounts')->with('success', 'Hesap başarıyla silindi');
        } else {
            return redirect()->route('accounts')->with('error', 'Hesap silinemedi');
        }
    }

    // Eski delete metodu (geriye dönük uyumluluk için)
    public function delete(Request $request, $id)
    {
        return $this->destroy($request, $id);
    }


    public function ajaxTransaction($id)
    {
        // Fetch account details
        $account = DB::table('accounts')->where('id', $id)->first();

        // Fetch associated transactions
        $transactions = DB::table('transactions')->where('account_id', $id)->get();

        // Return account and transactions as JSON
        return response()->json([
            'account' => $account,
            'transactions' => $transactions
        ]);
    }
}
