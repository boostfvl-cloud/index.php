  <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Panggil token & chat id dari file private
    include __DIR__ . '/private/config.php';

    // Fungsi sanitasi sederhana
    function bersihkan($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    // Ambil data form
    $kategori   = bersihkan($_POST['kategori']);
    $layanan    = bersihkan($_POST['layanan']);
    $link       = filter_var($_POST['link'], FILTER_SANITIZE_URL);
    $jumlah     = intval($_POST['jumlah']);
    $pembayaran = bersihkan($_POST['pembayaran']);

    // Upload bukti transfer
    $bukti_url = '❌ Tidak ada file diunggah.';
    if (!empty($_FILES['bukti']['tmp_name'])) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = basename($_FILES['bukti']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_path)) {
            $bukti_url = '✅ ' . $_SERVER['HTTP_HOST'] . '/uploads/' . $file_name;
        } else {
            $bukti_url = '❌ Gagal upload file.';
        }
    }

    // Format pesan Telegram
    $pesan = "📥 <b>Pesanan Baru Masuk</b>\n\n";
    $pesan .= "🔹 <b>Kategori:</b> $kategori\n";
    $pesan .= "🔹 <b>Layanan:</b> $layanan\n";
    $pesan .= "🔹 <b>Link:</b> $link\n";
    $pesan .= "🔹 <b>Jumlah:</b> $jumlah\n";
    $pesan .= "🔹 <b>Pembayaran:</b> $pembayaran\n";
    $pesan .= "🔹 <b>Bukti:</b> $bukti_url\n";

    // Kirim ke Telegram dengan cURL
    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $pesan,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Callback status
    if ($httpcode == 200) {
        echo "<div style='padding:15px; background:#d4edda; border-left:5px solid #28a745;'>
        ✅ Pesanan berhasil dikirim ke Telegram.
        </div>";
    } else {
        echo "<div style='padding:15px; background:#f8d7da; border-left:5px solid #dc3545;'>
        ❌ Gagal mengirim ke Telegram. Periksa token/chat_id.
        </div>";
    }
}
?>

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<style>
  .container {
  max-width: 400px;
  margin: auto;
  padding: 20px;
  background: linear-gradient(to bottom right, #4e54c8, #8f94fb);
  border-radius: 15px;
  color: white;
  font-family: 'Segoe UI', sans-serif;
}

.container h2 {
  text-align: center;
  margin-bottom: 20px;
  font-weight: bold;
  font-size: 24px;
}

.container label {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
}

.container input,
.container select {
  width: 100%;
  padding: 12px;
  font-size: 15px;
  border: none;
  border-radius: 8px;
  margin-bottom: 15px;
  box-sizing: border-box;
}

.container input[type="file"] {
  background-color: white;
  color: black;
}

.container .btn {
  width: 100%;
  padding: 14px;
  font-size: 16px;
  background-color: #1e3a8a;
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.container .btn:hover {
  background-color: #3749a3;
}

#totalHarga {
  margin-bottom: 20px;
  font-size: 18px;
  font-weight: bold;
  color: #00ff00;
  text-align: left;
}
</style>

<form method="post" enctype="multipart/form-data">

<div class="container">
    <h2>HALAMAN ORDER</h2>

    <label for="kategori">Kategori :</label>
    <select id="kategori" name="kategori" required>
      <option value="">-- Pilih Kategori --</option>
      <option value="tiktok">TikTok</option>
      <option value="instagram">Instagram</option>
      <option value="youtube">YouTube</option>
      <option value="facebook">Facebook</option>
    </select>

    <label for="layanan">Layanan :</label>
    <select id="layanan" name="layanan" required></select>

    <label for="link">Link Akun :</label>
    <input type="text" id="link" name="link" placeholder="Masukkan link akun atau postingan" required />

    <label for="jumlah">Jumlah :</label>
    <input type="number" id="jumlah" name="jumlah" required />
    <div id="infoJumlah"></div>

    <label for="bukti">Upload Bukti Transfer (Opsional) :</label>
    <input type="file" id="bukti" name="bukti" accept="image/*,.pdf" />
    <p style="margin-top:10px; color:#444; font-size:14px;">
📌 Jika mengalami kendala saat upload bukti, Anda bisa langsung kirim bukti transfer ke WhatsApp kami:  
<a href="https://wa.me/6287824860710" target="_blank" style="color:#25D366; text-decoration:none; font-weight:bold;">
👉 Klik di sini untuk kirim via WhatsApp
</a>
</p>
    
    <label for="pembayaran">Metode Pembayaran :</label>
    <select id="metode" name="pembayaran" required>
      <option value="GOPAY">GOPAY - 087824860710 (a.n. boostfvl)</option>
      <option value="DANA">DANA - 087824860710 (a.n. boostfvl)</option>
      <option value="ShopeePay">ShopeePay - 087824860710 (a.n. boostfvl)</option>
      <option value="OVO">OVO - 087824860710 (a.n. boostfvl)</option>
    </select>

    <div id="totalHarga">Total: Rp 0</div>

    <button type="submit" class="btn">Kirim Pesanan</button>
</div>
</form>

<!-- Include JS -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const kategoriSelect = document.getElementById("kategori");
    const layananSelect = document.getElementById("layanan");
    const jumlahInput = document.getElementById("jumlah");
    const infoJumlah = document.getElementById("infoJumlah");
    const totalHargaDiv = document.getElementById("totalHarga");

    const layananMap = {
      tiktok: [
        { value: "permanen", text: "TikTok Followers Permanen" },
        { value: "like", text: "TikTok Likes Permanen" },
        { value: "view", text: "TikTok Views Permanen" },
        { value: "indo", text: "TikTok Followers Indonesia" },
        { value: "cepat", text: "TikTok Followers Super Cepat" },
        { value: "live", text: "TikTok Live Views stay 90 mins" }
      ],
      instagram: [
        { value: "ig_followers", text: "Instagram Followers" },
        { value: "ig_indo",  text: "Instagram Followers Indo" },
        { value: "ig_likes", text: "Instagram Likes" },
        { value: "ig_views", text: "Instagram Views" },
        { value: "ig_story", text: "Instagram Story Views" },
        { value: "ig_reels", text: "Instagram Reels Views" }
      ],
      youtube: [
        { value: "yt_subs", text: "YouTube Subscribers" },
        { value: "yt_views", text: "YouTube Views" },
        { value: "yt_likes", text: "YouTube Likes" },
        { value: "yt_watchtime", text: "YouTube Watch Time" },
        { value: "yt_live", text: "YouTube Live Stream Views 30 menit" }
      ],
      facebook: [
        { value: "fb_followers", text: "Facebook Followers" },
        { value: "fb_likes", text: "Facebook Likes" },
        { value: "fb_views", text: "Facebook Views" },
        { value: "fb_react", text: "Facebook Post Reactions" },
        { value: "fb_share", text: "Facebook Post Shares" }
      ]
    };

    const batasLayanan = {
      permanen: { min: 100, max: 10000 },
      indo: { min: 100, max: 5000 },
      like: { min: 100, max: 10000 },
      view: { min: 1000, max: 1000000 },
      cepat: { min: 100, max: 10000 },
      live: { min: 50, max: 10000 },
      ig_followers: { min: 100, max: 10000 },
      ig_indo: { min: 100, max: 5000 },
      ig_likes: { min: 100, max: 10000 },
      ig_views: { min: 1000, max: 1000000 },
      ig_story: { min: 500, max: 50000 },
      ig_reels: { min: 500, max: 50000 },
      yt_subs: { min: 100, max: 10000 },
      yt_views: { min: 100, max: 1000000 },
      yt_likes: { min: 100, max: 10000 },
      yt_watchtime: { min: 100, max: 100000 },
      yt_live: { min: 100, max: 10000 },
      fb_followers: { min: 100, max: 10000 },
      fb_likes: { min: 100, max: 10000 },
      fb_views: { min: 1000, max: 100000 },
      fb_react: { min: 100, max: 10000 },
      fb_share: { min: 100, max: 100000 }
    };

    kategoriSelect.addEventListener("change", updateLayanan);
    layananSelect.addEventListener("change", updateMinMax);
    jumlahInput.addEventListener("input", updateHarga);

    function updateLayanan() {
      const kategori = kategoriSelect.value;
      layananSelect.innerHTML = "";
      if (layananMap[kategori]) {
        layananMap[kategori].forEach(item => {
          const opt = document.createElement("option");
          opt.value = item.value;
          opt.textContent = item.text;
          layananSelect.appendChild(opt);
        });
      }
      updateMinMax();
    }

    function updateMinMax() {
      const layanan = layananSelect.value;
      if (batasLayanan[layanan]) {
        const { min, max } = batasLayanan[layanan];
        jumlahInput.min = min;
        jumlahInput.max = max;
        jumlahInput.value = min;
        infoJumlah.innerText = `Jumlah harus antara ${min} - ${max}`;
      } else {
        jumlahInput.removeAttribute("min");
        jumlahInput.removeAttribute("max");
        jumlahInput.value = "";
        infoJumlah.innerText = "";
      }
      updateHarga();
    }

    function updateHarga() {
      const jumlah = parseInt(jumlahInput.value);
      const layanan = layananSelect.value;
      if (!isNaN(jumlah)) {
        const harga = hitungHarga(jumlah, layanan);
        totalHargaDiv.textContent = "Total: Rp " + harga.toLocaleString("id-ID");
      } else {
        totalHargaDiv.textContent = "Total: Rp 0";
      }
    }

    function hitungHarga(jumlah, layanan) {
      let harga = 0;
      if (layanan === "permanen" || layanan === "ig_followers") {
        if (jumlah < 200) harga = 50 * jumlah;
        else if (jumlah < 300) harga = 50 * jumlah;
        else if (jumlah < 400) harga = 50 * jumlah;
        else if (jumlah < 500) harga = 45 * jumlah;
        else if (jumlah < 600) harga = 40 * jumlah;
        else if (jumlah < 700) harga = 37 * jumlah;
        else if (jumlah < 800) harga = 35 * jumlah;
        else if (jumlah < 900) harga = 32.5 * jumlah;
        else if (jumlah < 1000) harga = 31 * jumlah;
        else harga = 35 * jumlah;
      } else if (["like", "ig_likes", "yt_likes"].includes(layanan)) {
        harga = 10 * jumlah;
      } else if (["view", "ig_views"].includes(layanan)) {
        harga = 0.5 * jumlah;
      } else if (layanan === "indo" || layanan === "ig_indo") {
        harga = 100 * jumlah;
      } else if (layanan === "cepat") {
        harga = 50 * jumlah;
      } else if (layanan === "live") {
        harga = 100 * jumlah;
      } else if (layanan === "ig_story") {
        harga = 6 * jumlah;
      } else if (layanan === "ig_reels") {
        harga = 8 * jumlah;
      } else if (layanan === "yt_subs") {
        harga = jumlah < 1000 ? 70 * jumlah : 65 * jumlah;
      } else if (layanan === "yt_views") {
        harga = jumlah >= 1000 ? 40 * jumlah : (jumlah / 1000) * 40000;
      } else if (layanan === "yt_watchtime") {
        harga = 70 * jumlah;
      } else if (layanan === "yt_live") {
        harga = 200 * jumlah;
      } else if (layanan === "fb_followers") {
        harga = (jumlah / 1000) * 25000;
      } else if (layanan === "fb_likes") {
        harga = (jumlah / 1000) * 35000;
      } else if (layanan === "fb_views") {
        harga = (jumlah / 1000) * 5500;
      } else if (layanan === "fb_react") {
        harga = (jumlah / 1000) * 25000;
      } else if (layanan === "fb_share") {
        harga = (jumlah / 1000) * 6000;
      }
      return Math.round(harga);
    }

    // Jalankan default saat pertama kali halaman dimuat
    updateLayanan();
  });
</script