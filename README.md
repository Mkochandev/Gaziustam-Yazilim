# GaziUstam — ücretsiz Cloudflare yayını

Üretim hedefi **Cloudflare Workers Free + D1 Free**. PHP hosting, VPS, Next.js, React veya ücretli R2 gerekmez. Mevcut ilk tasarımın CSS/görselleri korunur; admin ve sunucu kodu Cloudflare için yeniden uygulanmıştır.

## Hızlı yayın (Windows / macOS / Linux)

Node.js **22.13+** (testler için 24 önerilir) gerekir. Projenin kökünden:

```powershell
cd cloudflare
npm ci
npx wrangler login
npm run launch
```

`wrangler login` kendi tarayıcınızda Cloudflare yetkilendirmesini açar. Giriş kendi Cloudflare hesabınıza yapılır; bu proje bir hesap veya ücretli plan açmaz. Anahtar veya şifreyi GitHub'a ya da sohbete yazmayın.

`launch` sırasıyla:

1. `gaziustam` D1 veritabanını oluşturur veya aynı adlı mevcut veritabanını kullanır; gerçek ID'yi yerel `wrangler.jsonc` dosyasına yazar.
2. Yalnız bekleyen SQL migration'larını uygular; mevcut verileri sıfırlamaz.
3. Worker'ı yükler. İlk kurulumda oturum sırrı oluşana kadar kısa süre kurulum mesajı görülebilir.
4. Eksikse rastgele bir `SESSION_SECRET` üretip Cloudflare secret olarak kaydeder. Var olan sırrı değiştirmez.
5. Owner hesabı yoksa e-posta, isim ve şifre sorar. Şifre görünmez; en az 12 karakter. Veritabanında yalnız salted PBKDF2 hash tutulur.
6. Cloudflare'ın döndürdüğü `https://gaziustam.<hesabınız>.workers.dev` adresini açın. Yönetim: aynı adresin `/admin` yolu.

Cloudflare ilk hesap kurulumu/Workers alt alan adı seçimi isterse panelde tamamlayıp komutu tekrar çalıştırın. Kart veya ücretli plan istemi görürseniz devam etmeyin; bu yapı Workers ve D1 Free içindir.

Sonraki kod yayınları:

```powershell
cd cloudflare
npm ci
npm run deploy
```

Yerelde Cloudflare ayar dosyasında oluşan gerçek database_id'yi koruyun; dosyayı proje ekibinizle yönetebilirsiniz. Kimlik gizli token değildir. Birden fazla Cloudflare hesabınız varsa doğru hesabı seçin; gerektiğinde `CLOUDFLARE_ACCOUNT_ID` ortam değişkenini kullanın.

## Neler var?

- Sunucuda render edilen ilk tasarım, SVG yıldızlar ve mobil düzen.
- DE/EN/TR arayüzleri. Avrupa dil kayıtları; hazır olmayan dil etkinleştirilemez.
- Panelden haber/makale ve portföy; ayrı dil taslakları/yayın tarihleri; SEO ve slug 301'leri.
- Blogda tek düğmeli `yalnız mevcut dil / tüm diller` filtresi. Ana içerik başına tek kart, gerçek dil URL'si.
- Sürümlü teklif formu editörü, değişmeyen eski cevap snapshot'ları.
- Kalıcı talepler, durum/sorumlu, ekip notları, geçmiş, private dosya ekleri.
- Owner / editor / sales rolleri, CSRF, oturum, spam limiti ve isteğe bağlı Turnstile.
- İçerik API'si için kapsamlı ve iptal edilebilir erişim anahtarları.

## İlk yayın sonrasında

1. `/admin` üzerinden blog/portföy içeriklerini ekleyin.
2. Teklif Formu'nda alanları düzenleyin → **Yeni taslak kaydet** → **Seçili taslağı yayınla**.
3. Bir test talebi gönderip Talepler ekranında göründüğünü kontrol edin.
4. Ayarlar'dan işletmenize ait yasal ve gizlilik metinlerini girin. Bu metinler başlangıçta boş/taslak açıklamalıdır.
5. Kendi domaininiz varsa Cloudflare dashboard → Workers & Pages → gaziustam → Settings → Domains & Routes → Custom Domain üzerinden bağlayın. Domain satın alma ücreti bu projeye dahil değildir; ücretsiz workers.dev adresi yeterlidir.

E-posta servisi **bağlı değildir**. Talepler kaybolmadan panelde saklanır; mail gönderilmiş gibi sahte başarı gösterilmez. Müşteriye dönüşü paneldeki iletişim bilgileriyle yapın. SMTP yerine ileride bir e-posta API'si ayrıca bağlanabilir.

Yeni Cloudflare veritabanı boştur; Laravel'in bilgisayarınızdaki SQLite/MySQL verileri ve admin şifreleri otomatik taşınmaz. Kaynak verileri silmeyin. Gerçek kayıtlar varsa veri aktarımı ayrıca yapılmalıdır.

## Yerel çalıştırma

`cloudflare` dizininde `.dev.vars.example` dosyasını `.dev.vars` olarak kopyalayın; `SESSION_SECRET` için 32+ karakter rastgele değer girin.

```powershell
npm ci
npm run db:local
npm run owner -- --local
npm run dev
```

Tarayıcıda Wrangler'ın yazdığı adresi açın. İsteğe bağlı ve açıkça örnek olarak etiketlenmiş içerikler:

```powershell
npm run seed:demo -- --local
```

Gerçek yayında da konsept içerikleri istiyorsanız bilinçli olarak `--remote` seçebilirsiniz. Normal yayın komutu sahte müşteri/proje oluşturmaz.

## Kontroller

```powershell
npm run check
npm test
npx wrangler deploy --dry-run
```

14 feature testi, gerçek Wrangler/D1 migration ve tarayıcı akışları doğrulanmıştır. Detaylar: [Cloudflare doğrulama](cloudflare/VERIFICATION.md).

## Ücretsiz sınırlar ve kapsam

Ücretsiz planın kullanım kotaları vardır; limitsiz dinamik sunucu değildir. Kota aşımı ücretli plana geçmeden hata/erişim kısıtına yol açabilir. Kod ücretli plana otomatik geçiş yapmaz. Statik varlıklar Worker öncesi sunulur. Trafik ve D1 kullanımını Cloudflare panelinden izleyin.

Görseller/ekler ücretli depolama zorunluluğu olmaması için D1 BLOB parçalarında saklanır. En fazla 3 × 5 MB talep eki; panel görseli 5 MB. Sık görsel yüklemede D1 depolama/yazma kotası daha hızlı dolar. Büyük medya arşivi için ileride ayrı çözüm değerlendirilir; R2 otomatik açılmaz.

Panel listeleri ilk sürümde son 250 talep/300 içerik ve arama/filtre sunar. İlk aktif diller DE/EN/TR; diğer dilde taslak eklenebilir, fakat public kullanım için arayüz ve form çevirileri koddan tamamlanmalıdır. Zengin içerik editörü HTML araç çubuklu metin editörüdür; Filament değildir. Tek kayıtta en fazla 10 değişen dil sürümü kaydedilir.

- [Cloudflare kurulum ayrıntıları](cloudflare/README.md)
- [İçerik API'si](cloudflare/API.md)
- [Yedek ve aktarım notları](cloudflare/OPERATIONS.md)
- [Eski Laravel kurulum rehberi](docs/LARAVEL_README.md)

Eski Laravel dosyaları geri dönüş ve veri aktarım referansı için korunmuştur. Cloudflare'a yayınlanan uygulama yalnız `cloudflare/` dizinidir. Cloudflare Git bağlantısıyla kurulum yaparsanız Root directory `cloudflare` olmalıdır; ilk DB/secret/owner kurulumu için yukarıdaki CLI yolu daha kolaydır.
