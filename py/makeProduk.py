import csv
import random

input_file = "data_jamu.csv"
output_file = "produk_seeder.txt"

produk_unik = set()
hasil = []
id_produk = 1

with open(input_file, mode="r", encoding="utf-8") as file:
    reader = csv.DictReader(file, delimiter=";")

    for row in reader:
        nama_jamu = row["NAMA_JAMU"].strip()
        khasiat = row["KHASIAT"].strip()

        # case insensitive duplicate checking
        nama_normalized = nama_jamu.lower().strip()

        if nama_normalized in produk_unik:
            continue

        produk_unik.add(nama_normalized)

        nama_jamu = nama_jamu.replace("'", "\\'")
        khasiat = khasiat.replace("'", "\\'")

        harga_satuan = random.randrange(5000, 100001, 1000)

        data = f"""[
    'id_produk' => {id_produk},
    'id_kategori' => {random.randint(1, 3)},
    'id_satuan' => {random.randint(1, 3)},
    'kode_produk' => 'PRD{id_produk:03d}',
    'nama_produk' => '{nama_jamu}',
    'harga_satuan' => {harga_satuan},
    'stok_terkini' => {random.randint(0, 1000)},
    'stok_minimum' => {random.randint(50, 100)},
    'deskripsi' => '{khasiat}',
],"""

        hasil.append(data)
        id_produk += 1

with open(output_file, mode="w", encoding="utf-8") as file:
    file.write("\n".join(hasil))

print(f"Seeder berhasil dibuat di file: {output_file}")
print(f"Total produk unik: {len(hasil)}")
