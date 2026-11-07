# Cloudflare geçişi doğrulama

7 Kasım 2026; eski Laravel test sonuçlarından ayrı değerlendirilmiştir.

## Geçen kontroller

- `npm run check`: Worker ve admin JavaScript syntax kontrolü.
- `npm test`: 14 test başarılı. Dil seçimi/tek kart, taslak ve planlı yayın, arşiv, slug 301 ve çakışma, SVG SSR, gerçek talep ve idempotency, CSRF, rol sınırları, form sürümleme/snapshot, API publish kapsamı, HTML temizliği, login/session, dolu içerik listesi, external_id idempotency, private PDF ve sahte dosya reddi.
- Gerçek Wrangler local D1: iki migration ve opsiyonel demo import başarılı.
- Wrangler local Worker tarayıcı kontrolü: DE ana sayfa, TR gerçek form POST'u ve referans, admin talep listesi, yazı oluşturma, ikinci dil taslağı ekleme, form label değişimi, taslak kaydı/yayınlama ve public forma yansıması.
- 390 px görsel kontrol: aynı SSR HTML'i ve CSS ile site/panel mobil düzeni. Panel GET verisi yerel D1'den alınmıştır; dar ekran kopyaları yalnız QA içindi ve repodan çıkarıldı.
- `wrangler deploy --dry-run`: yaklaşık 475 KiB sunucu paketi; asset dizini yalnız CSS/JS/SVG/WebP. Gerçek Cloudflare hesabında üretim deploy değildir.
- Yerel test oturumu açmak için kullanılan geçici QA kodu, test hesap SQL'i ve mobil snapshot dosyaları teslim kaynaklarından çıkarıldı.

## Henüz doğrulanmayanlar

- Kullanıcının Cloudflare hesabında D1 oluşturma, account yetkilendirme, gerçek workers.dev URL'sine yayın ve üretim smoke testleri. Bu oturumda Cloudflare CLI yetkisi yok; bağlı tarayıcı eklentisine erişim de bulunmadı.
- Gerçek Free plan CPU/istek/kota davranışı, alan adı, e-posta servisi, gerçek veri aktarımı ve uzak yedek geri yükleme.
- E-posta servisi bağlı değildir; varmış gibi test sonucu sunulmaz. Mobil kontrolde bütün telefon/ekran matrisi taranmamıştır.

GitHub Actions bu kontrollerin otomatik bölümünü her PR/push'ta tekrar çalıştırır; deploy etmez.
