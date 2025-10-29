@extends('layouts.app')

@section('content')
<style>
  .hero{position:relative;background:url('https://tse2.mm.bing.net/th/id/OIP.Yd_U2pKE6orot9FcUx6xRgHaEv?w=1200&h=769&rs=1&pid=ImgDetMain&o=7&rm=3') center/cover no-repeat;border-radius:12px;min-height:320px;color:#fff;display:flex;align-items:center}
  .hero::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.35),rgba(0,0,0,.45));border-radius:12px}
  .hero-inner{position:relative;padding:28px}
  .section-title{font-size:22px;margin:12px 0}
  .grid-3{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
  .card.feature{display:flex;gap:12px;align-items:flex-start}
  .badge-pill{display:inline-block;background:#0ea5e9;color:#fff;padding:4px 10px;border-radius:999px;font-size:12px}
  .muted-2{color:#475569}
  .gallery img{width:100%;height:140px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}
</style>

<div class="hero">
  <div class="hero-inner">
    <span class="badge-pill">JET505</span>
    <h1 style="font-size:32px;margin:8px 0 4px">Sewa Jet Tempur & Jet Privat dengan Mudah</h1>
    <p style="max-width:720px;color:#0ea5e9;">Nikmati pengalaman terbang kelas dunia. Proses cepat, armada lengkap, dukungan 24/7. Cocok untuk misi khusus, VIP transport, hingga event spesial.</p>
    <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap">
    
    </div>
  </div>
  </div>

<div class="card" style="margin-top:16px">
  <h2 class="section-title">Kenapa Memilih JET505?</h2>
  <div class="grid-3">
    <div class="card feature">
      <div class="badge-pill" style="background:#16a34a">Aman</div>
      <div>
        <b>Certified & Maintained</b>
        <div class="muted-2">Armada terawat, pilot berpengalaman, compliance ketat.</div>
      </div>
    </div>
    <div class="card feature">
      <div class="badge-pill" style="background:#f59e0b">Cepat</div>
      <div>
        <b>Proses Instan</b>
        <div class="muted-2">Cari unit, booking, dan bayar dalam hitungan menit.</div>
      </div>
    </div>
    <div class="card feature">
      <div class="badge-pill" style="background:#6366f1">Fleksibel</div>
      <div>
        <b>Rute & Jadwal Custom</b>
        <div class="muted-2">Atur destinasi, tanggal, dan durasi sesuai kebutuhan.</div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <h2 class="section-title">Armada Populer</h2>
  <div class="grid-3 gallery">
    <img src="https://th.bing.com/th/id/R.e45c0a4868bbc451180e0ac433db0d13?rik=fHt4jks9PCvZfQ&riu=http%3a%2f%2f3.bp.blogspot.com%2f-aIpy6ROzBF0%2fVbPG11d2RxI%2fAAAAAAAAF0c%2feVnIzmQxA6I%2fs1600%2fFoto%252Bgambar%252BPesawat%252BTempur%252BF-16%252BFighting%252BFalcon%252B01.jpg&ehk=3gHhjm56cedYbvzP73iJkowx78PTrLUQoi%2bY4qNoI0Q%3d&risl=&pid=ImgRaw&r=0" alt="F-16">
    <img src="https://tse2.mm.bing.net/th/id/OIP.jfUI7zszhnO6qDl1E8PVfwHaER?rs=1&pid=ImgDetMain&o=7&rm=3" alt="Rafale">
    <img src="https://tse4.mm.bing.net/th/id/OIP.HEssWWxPyi-ey08yCmcg1gAAAA?rs=1&pid=ImgDetMain&o=7&rm=3" alt="Eurofighter">
  </div>
  <div style="margin-top:12px"><a class="btn" href="/units">Lihat Semua Unit</a></div>
</div>

<div class="card">
  <h2 class="section-title">Cara Kerja</h2>
  <ol style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;list-style:none;padding:0;margin:0">
    <li class="card"><b>1. Pilih Jet</b><div class="muted-2">Telusuri katalog dan cek ketersediaan.</div></li>
    <li class="card"><b>2. Ajukan Sewa</b><div class="muted-2">Lengkapi data peminjam dan detail perjalanan.</div></li>
    <li class="card"><b>3. Pembayaran</b><div class="muted-2">Bayar dengan metode favorit Anda.</div></li>
    <li class="card"><b>4. Terbang</b><div class="muted-2">Tim kami mengurus seluruh persiapan.</div></li>
  </ol>
</div>

@endsection


