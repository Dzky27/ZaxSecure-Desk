/* Data mock ZaxSecure Desk. Semua data DEMO, bukan data nyata. */
(function(){
  const CATS=['Phishing & Penipuan','Kebocoran Data','Akun Diretas','Malware','Lainnya'];
  const ADMINS=['ZaxVoldigoad','Bima Prasetyo','Sinta Maharani'];
  const ME={admin:{name:'ZaxVoldigoad',role:'Administrator',email:'zaxvoldigoad@zaxsecure.id'},user:{name:'Aulia Rahma',role:'Pengguna',email:'aulia.rahma@mail.com'}};
  const POOL=[
    ['Email phishing mengatasnamakan layanan bank',0,'https://verifikasi-akun-bank.example.com/login','Pelapor menerima email yang meminta pembaruan data rekening melalui tautan. Domain pengirim tidak sama dengan domain resmi bank.'],
    ['Tautan undian berhadiah menyebar di WhatsApp',0,'https://hadiah-nasional.example.net/klaim','Pesan berantai di grup WhatsApp meminta pengguna mengisi data pribadi dan kode OTP untuk mengklaim hadiah.'],
    ['Data nasabah muncul di forum jual beli',1,'https://forum.example.org/thread/4412','Pelapor menemukan sampel data yang memuat nama, nomor telepon, dan alamat pada sebuah forum publik.'],
    ['Akun Instagram organisasi diambil alih',2,'https://instagram.com/organisasi.kampus','Akun resmi organisasi tidak dapat diakses, email pemulihan berubah, dan terdapat unggahan yang tidak dikenal.'],
    ['Lampiran .exe pada email tagihan palsu',3,'','Email berjudul tagihan membawa lampiran berformat .exe. Dua komputer staf sempat membukanya sebelum dilaporkan.'],
    ['Ransomware menyerang komputer laboratorium',3,'','Berkas pada beberapa komputer laboratorium berubah ekstensi dan muncul pesan permintaan tebusan.'],
    ['Percobaan login mencurigakan pada akun portal akademik',2,'https://portal.example.ac.id','Terdapat notifikasi login dari perangkat dan lokasi yang tidak dikenal pada tengah malam.'],
    ['Spreadsheet data mahasiswa terbagikan secara publik',1,'https://docs.example.com/spreadsheets/d/abc123','Tautan berbagi spreadsheet diatur ke publik sehingga siapa pun dapat melihat data nilai dan kontak.'],
    ['Toko online palsu memakai nama merek terkenal',0,'https://toko-promo.example.shop','Situs menawarkan diskon besar dan hanya menerima transfer ke rekening pribadi.'],
    ['Aplikasi APK palsu diduga menyebarkan spyware',3,'https://apk-gratis.example.xyz/app','Aplikasi dibagikan lewat tautan di luar toko resmi dan meminta akses kontak serta SMS.'],
    ['SMS penipuan paket kurir tertahan',0,'https://cek-paket.example.top/track','SMS meminta pengguna membayar biaya tambahan melalui tautan pendek agar paket dapat dikirim.'],
    ['Ekstensi peramban meminta izin berlebihan',4,'','Ekstensi hasil unduhan meminta akses ke seluruh data situs yang dikunjungi tanpa alasan yang jelas.']
  ];
  const STATUS=['Pending','Pending','Diproses','Pending','Diproses','Diproses','Pending','Diproses','Pending','Diproses','Selesai','Diproses','Selesai','Selesai','Diproses','Selesai','Selesai','Pending','Selesai','Selesai','Diproses','Selesai','Selesai','Selesai'];
  const PRI=['High','Critical','Medium','High','Medium','Low','Critical','Medium','High','Low','Medium','High','Low','Medium','High','Low','Medium','Low','High','Medium','Low','Medium','Low','Low'];
  const REP=['Aulia Rahma','Dimas Nugraha','Felisa Putri','Raka Wijaya','Nadia Safira','Fajar Hidayat'];
  const pad=n=>String(n).padStart(2,'0');
  const iso=d=>`${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
  const incidents=STATUS.map((st,i)=>{
    const p=POOL[(i*5+i%3)%POOL.length];
    return {id:'ZSD-2026-'+String(42-i).padStart(4,'0'),title:p[0],category:CATS[p[1]],priority:PRI[i],status:st,
      date:iso(new Date(2026,10,14-i*7)),reporter:i%4===0?'Aulia Rahma':REP[(i%5)+1],
      assignee:st==='Pending'?'':ADMINS[i%3],url:p[2],desc:p[3],
      occurred:iso(new Date(2026,10,13-i*7)),notes:st==='Pending'?[]:[{by:ADMINS[i%3],text:'Laporan telah ditinjau dan bukti awal dikumpulkan.',at:iso(new Date(2026,10,15-i*7))}]};
  });
  try{const extra=JSON.parse(localStorage.getItem('zsd_new_incidents')||'[]');extra.reverse().forEach(e=>incidents.unshift(e));}catch(e){}
  const users=[
    ['ZaxVoldigoad','zaxvoldigoad@zaxsecure.id','Administrator',true,'2026-01-12'],
    ['Bima Prasetyo','bima.prasetyo@zaxsecure.id','Administrator',true,'2026-01-20'],
    ['Sinta Maharani','sinta.maharani@zaxsecure.id','Administrator',true,'2026-02-03'],
    ['Aulia Rahma','aulia.rahma@mail.com','Pengguna',true,'2026-03-08'],
    ['Dimas Nugraha','dimas.nugraha@mail.com','Pengguna',true,'2026-03-15'],
    ['Felisa Putri','felisa.putri@mail.com','Pengguna',true,'2026-04-02'],
    ['Raka Wijaya','raka.wijaya@mail.com','Pengguna',false,'2026-04-19'],
    ['Nadia Safira','nadia.safira@mail.com','Pengguna',true,'2026-05-11'],
    ['Fajar Hidayat','fajar.hidayat@mail.com','Pengguna',true,'2026-06-23'],
    ['Maya Anggraini','maya.anggraini@mail.com','Pengguna',false,'2026-08-05']
  ].map((u,i)=>({id:i+1,name:u[0],email:u[1],role:u[2],active:u[3],joined:u[4]}));
  const catDesc=['Email, SMS, atau situs palsu yang meniru layanan resmi','Data pribadi atau internal yang terekspos tanpa izin','Akun diambil alih atau diakses pihak tidak berwenang','Perangkat lunak berbahaya, ransomware, spyware','Insiden keamanan lain di luar kategori utama'];
  const categories=CATS.map((c,i)=>({id:i+1,name:c,desc:catDesc[i],active:true}));
  const LA=[['Login','Autentikasi','Berhasil masuk ke sistem'],['Ubah status','Insiden','Mengubah status tiket {t} menjadi Diproses'],['Tugaskan','Insiden','Menugaskan tiket {t} kepada administrator'],['Buat laporan','Insiden','Membuat laporan insiden baru {t}'],['Ubah kategori','Kategori','Memperbarui deskripsi kategori insiden'],['Nonaktifkan akun','Pengguna','Menonaktifkan akun pengguna'],['Ekspor CSV','Laporan','Mengekspor data laporan insiden'],['Ubah kata sandi','Autentikasi','Memperbarui kata sandi akun'],['Logout','Autentikasi','Keluar dari sistem']];
  const IPS=['10.12.4.21','10.12.4.35','172.16.8.9','192.168.1.14','103.28.12.47'];
  const logs=Array.from({length:36},(_,i)=>{const a=LA[(i*7)%LA.length];const t=incidents[i%incidents.length].id;
    return {id:i+1,user:users[i%4<3?i%3:3+(i%4)].name,action:a[0],category:a[1],desc:a[2].replace('{t}',t),ip:IPS[i%IPS.length],time:`2026-11-${pad(14-Math.floor(i/4))} ${pad(8+(i*3)%12)}:${pad((i*17)%60)}`};});
  const notifications=[
    {t:'Laporan kritis baru: '+incidents[1].id,s:'2 jam lalu',n:true},
    {t:'Status '+incidents[2].id+' diperbarui menjadi Diproses',s:'5 jam lalu',n:true},
    {t:'Pengguna baru mendaftar: Maya Anggraini',s:'Kemarin',n:true},
    {t:'Ekspor laporan bulanan selesai',s:'2 hari lalu',n:false}
  ];
  const MN=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  function monthly(n){const out={labels:[],values:[]};for(let k=n-1;k>=0;k--){const d=new Date(2026,10-k,1);out.labels.push(MN[d.getMonth()]);
    out.values.push(incidents.filter(i=>i.date.startsWith(`${d.getFullYear()}-${pad(d.getMonth()+1)}`)).length);}return out;}
  function addLocal(inc){incidents.unshift(inc);try{const a=JSON.parse(localStorage.getItem('zsd_new_incidents')||'[]');a.unshift(inc);localStorage.setItem('zsd_new_incidents',JSON.stringify(a));}catch(e){}}
  window.ZSD={CATS,ADMINS,ME,incidents,users,categories,logs,notifications,monthly,addLocal,MN};
})();
