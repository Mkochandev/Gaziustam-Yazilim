# Doğrulama ve teslim durumu

7 Kasım 2026 tarihinde yerel doğrulama tamamlandı.

| Kontrol | Sonuç |
| --- | --- |
| SQLite feature/unit paketi | 14 test, 148 doğrulama geçti |
| MySQL 8.0.46 aynı paket | 14 test, 148 doğrulama geçti |
| Boş kontrol dizininde kurulum | Kilitlerden composer install ve npm ci, migration, seed ve Vite build geçti |
| Temiz kurulum rota testi | 1 test, 28 doğrulama geçti |
| Composer kilidi | composer validate başarılı, bağımlılık çözümünde güvenlik duyurusu bulunmadı |
| Laravel yayın derlemeleri | route:cache ve view:cache başarılı |
| Gerçek kuyruk | Database queue işi yerel log mail sürücüsüyle DONE oldu |
| Yedek ve geri yükleme | Ayrı MySQL test kopyasında 24 tablo geri yüklendi; içerik, çeviri ve form tablolarının checksum değerleri eşleşti |
| Ziyaretçi tasarımı | DE/EN/TR × 360/390/768/1280/1440 px üzerinde 15 kontrol geçti |
| Panel tasarımı | Form editörü ve zengin editör 390/768/1440 px üzerinde taşmasız |
| Gerçek HTTP formu | JavaScript kapalı gönderim referans üretti ve panelde göründü |
| CSRF | Tokensız POST 419 ile reddedildi |
| Tarayıcı konsolu | Son panel/teklif akışında yakalanan JavaScript hatası yok |

Testler dil önceliğini, tek kart/ana içerik davranışını, taslak ve gelecek yayın gizliliğini, slug 301 ve rezervasyonunu, signed önizlemeyi, rol sınırlarını, form sürümünü ve snapshot'ını, idempotency'yi, dosya doğrulamasını, private indirmeyi, durum/sorumlu geçmişini, mail arızasını, transaction rollback ve dosya temizliğini, API kapsamlarını, token iptalini, medya kullanım korumasını, zengin editörü ve eksik dil etkinleştirme engelini kapsar.

Windows'ta tarayıcı ve PHPUnit aynı Blade cache dizininde eşzamanlı derleme yapınca dosya kilidi hatası gözlendi. PHPUnit artık süreç başına ayrı derleme dizini kullanır. Son iki veritabanı turu bu düzenlemeyle geçti.

## Faz sonuçları

- Faz 0 tamamlandı. İlk kaynağın gerçek kodu/görselleri incelendi, bağımlılıklar kilitlendi, temiz kurulum doğrulandı.
- Faz 1 tamamlandı. Blade ana sayfa, özgün SVG ve görseller, üç dil ve responsive kontroller geçti.
- Faz 2 tamamlandı. Yetkili içerik yönetimi, dil sürümleri, zengin editör, yayın sorguları ve gerçek blog filtresi çalışıyor.
- Faz 3 tamamlandı. Üç sütunlu alan editörü, anlık önizleme, sürümleme, doğrulama ve atomik yayınlama çalışıyor.
- Faz 4 tamamlandı. Kalıcı talep/cevap/ek kaydı, snapshot, idempotency, kuyruk ve ekip geçmişi doğrulandı.
- Faz 5 tamamlandı. Portföy, kategori, galeri sırası, yerelleştirilmiş alt metinler ve WebP önizlemeleri hazır. 8 megapikseli aşan görseller bellek sınırı nedeniyle özgün dosyadan sunulur.
- Faz 6 tamamlandı. Sanctum API, örnek istemci, SEO ve gerçek dil URL'leri doğrulandı. İçerik sorgu cache'i kapalıdır.
- Faz 7'nin yerel doğrulaması ve yayın belgeleri tamamlandı. Gerçek staging/production işlemleri bekliyor.

## Yayın için bekleyenler

Gerçek hosting/VPS erişimi, domain ve HTTPS, SMTP erişimi/gönderen doğrulaması, gerekiyorsa Turnstile anahtarları, işletme bilgileri ve onaylı hukuki/gizlilik metinleri gerekli. Staging smoke, üretim cookie/proxy/cache kontrolleri ve gerçek yedek depolama servisi bu bilgilerle uygulanmalı.

Geri yükleme denemesi örnek MySQL verileriyle yapıldı. Gerçek müşteri verileri ve private medya arşiviyle üretim geri yükleme tatbikatı ayrıca yapılmalıdır. Gerçek SMTP teslimatı ve Lighthouse ölçümü yapılmadı.

Haber/portföy için ortak içerik modeli, galeri yolu dizisi ve cache tercihi DECISIONS.md içinde açıklanmıştır. Yerel test sürücüleri dış servis teslimatı olarak sunulmaz.

