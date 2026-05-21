import csv
import random
import math

input_file = "data_jamu.csv"
output_file = "eoq_seeder.txt"

produk_unik = set()
hasil = []

id_eoq = 1
id_produk = 1

with open(input_file, mode="r", encoding="utf-8") as file:
    reader = csv.DictReader(file, delimiter=";")

    for row in reader:
        nama_jamu = row["NAMA_JAMU"].strip()

        # case insensitive duplicate check
        nama_normalized = nama_jamu.lower()

        if nama_normalized in produk_unik:
            continue

        produk_unik.add(nama_normalized)

        # Generate data EOQ
        demand = random.randint(1000, 50000)
        biaya_pemesanan = random.randrange(10000, 100001, 1000)
        biaya_penyimpanan = random.randrange(1000, 10001, 1000)

        # Hitung EOQ
        eoq = math.sqrt((2 * demand * biaya_pemesanan) / biaya_penyimpanan)

        data = f"""[
    'id_eoq' => {id_eoq},
    
    'id_produk' => {id_produk},
    'permintaan_tahunan' => {demand},
    'biaya_pemesanan' => {biaya_pemesanan},
    'biaya_penyimpanan' => {biaya_penyimpanan},
    'eoq' => {round(eoq)},
],"""

        hasil.append(data)

        id_eoq += 1
        id_produk += 1

with open(output_file, mode="w", encoding="utf-8") as file:
    file.write("\n".join(hasil))

print(f"Seeder berhasil dibuat di file: {output_file}")
print(f"Total data EOQ: {len(hasil)}")
