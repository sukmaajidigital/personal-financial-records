<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('user_id', $this->user()->id),
            ],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'type' => ['required', 'string', Rule::in(['income', 'expense'])],
            'date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.integer' => 'Kategori tidak valid.',
            'category_id.exists' => 'Kategori yang dipilih tidak ditemukan.',
            'description.required' => 'Deskripsi mohon diisi.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
            'amount.required' => 'Jumlah wajib diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'amount.min' => 'Jumlah minimal Rp 0,01.',
            'amount.max' => 'Jumlah terlalu besar.',
            'type.required' => 'Tipe transaksi wajib dipilih.',
            'type.string' => 'Tipe transaksi tidak valid.',
            'type.in' => 'Tipe transaksi harus pemasukan atau pengeluaran.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
        ];
    }
}
