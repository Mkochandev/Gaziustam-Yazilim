> **Güncel yayın hedefi:** Ücretsiz Cloudflare Workers + D1. Uygulanmış Cloudflare sürümü `cloudflare/` dizininde; başlangıç için kökteki README.md dosyasını okuyun. Aşağıdaki Laravel kararları/görevleri önceki uygulamanın tarihçesidir ve artık hosting talimatı değildir.

# Mimari kararlar

- Kamuya açık HTML Blade'de üretilir. Arama, dil filtresi, kategori ve sayfalama GET parametreleridir. Teklif formu CSRF korumalı POST ile çalışır.
- Laravel 12 ve Filament 5 mevcut PHP 8.2 ile Composer üzerinden çözümlendi. Livewire 4 panel ve zengin editör içindir. Alpine ayrıca başlatılmaz.
- Haber, makale ve portföy contents tablosunda türle ayrılır. Her dil translations tablosunda bağımsız satırdır. Ayrı Post ve Project tabloları yerine ortak model seçildi. Yayın, slug geçmişi ve API aynı servisleri kullanır.
- Galeri sırası ana kayıtta dosya yolları dizisidir. Dil bazlı alt metinler ilgili çeviride tutulur. Medya kataloğu ayrı tablodadır. Kapak ve galeri yüklemeleri kataloğa kaydedilir. Silmede içerik kullanımı denetlenir.
- Kategori ve kategori çevirileri ayrı tablolardır. Altı hizmet kodu teklif eşleştirmesi için sabittir.
- Form şeması sürümlü JSON'dur. Düzenleme yeni taslak oluşturur. Yayınlanan sürümün şeması değişmez. Yayınlamada form satırı kilitlenir. Emekli sürüm sonraki 24 saat boyunca gönderilebilir.
- Talep, cevaplar, ek kayıtları ve database queue işi aynı transaction içindedir. Worker yalnız commit edilmiş işi görebilir. Böylece kayıt sonrasında ayrı kuyruğa yazma hatasıyla bildirim kaybolmaz. Database queue uygulama ile aynı DB bağlantısında tutulmalıdır.
- Snapshot yerelleştirilmiş etiketleri ve seçenekleri saklar. Idempotency anahtarı benzersizdir. Tekrar deneme aynı referansı döndürür. Private dosyalar yetkili controller üzerinden indirilir.
- İçerik sorguları önbelleğe alınmaz. Yayın zamanı, arşivleme ve silme her istekte güncel görünür. Public HTML, CSRF ve session nedeniyle private, no-store döner. Statik görseller/CSS CDN'de tutulabilir.
- Sanctum okuma, yazma ve yayınlama kapsamlarını ayırır. Yayındaki içeriğe yazma ayrıca yayınlama kapsamı gerektirir. provider ve external_id birlikte benzersizdir.
- DE varsayılandır. DE/EN/TR aktiftir. Kaynaktaki diğer 41 dil kapalıdır. Dil etkinleştirme arayüz, doğrulama ve aktif form çevirisini denetler.
- Yerel mail log sürücüsüdür. Staging, SMTP, domain, Cloudflare ve Turnstile hesapları dağıtım aşamasında yapılandırılır.

