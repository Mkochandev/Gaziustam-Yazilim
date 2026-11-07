# Yayına hazırlık

PHP-FPM/Apache veya Laravel uyumlu bir servis, MySQL 8, queue worker ve scheduler gereklidir. Web kökü public dizini olmalıdır. Uygulama dizinine web erişimi vermeyin.

## Yayın sırası

1. Veritabanı ve storage yedeği alın. Yeni release dizininde composer install --no-dev --optimize-autoloader ve npm ci && npm run build çalıştırın.
2. Sunucunun .env ve kalıcı storage dizinini bağlayın. APP_ENV=production, APP_DEBUG=false, APP_URL=https://alanadi, SESSION_SECURE_COOKIE=true, SESSION_ENCRYPT=true ayarlayın. APP_KEY ilk kurulumdan sonra korunur.
3. DB erişimini uygulamaya özel kullanıcıyla tanımlayın. QUEUE_CONNECTION=database kullanın. DB_QUEUE_CONNECTION boş kalmalı veya aynı uygulama bağlantısı olmalı.
4. php artisan migrate --force ve php artisan db:seed --force çalıştırın. Üretim seed'i müşteri/örnek içerik oluşturmaz.
5. php artisan storage:link, php artisan config:cache, php artisan route:cache, php artisan view:cache çalıştırın.
6. Worker'ı yeniden başlatın. Web sunucusunu yeni release'e yönlendirin. Smoke testlerini tamamlayın.

Kaynak kod, .env, log ve private ekler yalnız uygulama kullanıcısına açık olmalıdır. public/storage bağlantısı yalnız storage/app/public dizinine gider. storage/app/private altında web bağlantısı oluşturmayın.

## Worker ve scheduler

Supervisor veya systemd ile aşağıdaki süreci otomatik başlatın. Aynı kullanıcı storage ve cache dizinlerine yazabilmeli.

    php /srv/gaziustam/artisan queue:work database --sleep=3 --tries=4 --timeout=60 --max-time=3600

Queue retry_after değeri timeout'tan büyük olmalıdır. Varsayılan 90 saniyedir. Deploy sonrasında php artisan queue:restart çalıştırın. Başarısız işler Owner Ayarlar ekranında görülebilir ve yeniden kuyruğa alınabilir. CLI alternatifleri

    php artisan queue:failed
    php artisan queue:retry JOB_UUID

Dakikalık cron

    * * * * * cd /srv/gaziustam && php artisan schedule:run >> /dev/null 2>&1

studio:prune günlük çalışır ve ENQUIRY_RETENTION_DAYS süresini aşan talepleri, eklerini ve bağlı cevapları temizler. Hukuki saklama gereksinimleri doğrulanmadan gerçek müşteri verileriyle scheduler devreye alınmamalıdır.

## SMTP ve Turnstile

MAIL_MAILER=log geliştirme içindir. SMTP host, port, şema, kullanıcı, parola, gönderen adresi ve ENQUIRY_NOTIFY_TO gerçek değerlerle yapılandırılır. Kuyruk işinde yalnız referans ve panel bağlantısı gönderilir. Mail arızası talebi geri almaz. Gerçek SMTP teslimatı, SPF/DKIM/DMARC ve alıcı kutusu kontrolü bu ortamda yapılmadı.

Turnstile kullanılacaksa iki anahtarı ve izinli hostname'i sağlayıcıda tanımlayın. Site anahtarı public, secret yalnız sunucudadır. Başarısız doğrulama kullanıcıya hata döner.

## HTTPS ve Cloudflare

TLS sertifikası origin üzerinde de geçerli olmalı, Cloudflare Full (strict) kullanılmalı. Proxy güveni yalnız kendi reverse proxy adreslerinize tanımlanmalı. Gerçek client IP ve HTTPS algısı staging üzerinde kontrol edilmelidir.

HTML için cache bypass kullanın. /admin*, /livewire*, /api*, POST, oturum cookie'si taşıyan yanıtlar, teklif gönderimleri ve private indirme uçları shared cache'e alınmamalıdır. Mevcut Laravel public HTML yanıtları private, no-store döner. CSS, JS ve public görseller sürümlü statik cache kullanabilir.

upload_max_filesize en az 5M, post_max_size en az 20M olmalı. Web sunucusu body limitini buna göre ayarlayın. Başlangıçta 2 worker ile yük testi yapın. Trafik ve dosya hacmi ölçülmeden kapasite garantisi verilmez.

## Yedek ve geri yükleme

Günlük MySQL dump ve public/private storage yedeği alın. Yedeği şifreli, sunucudan ayrı depoda ve erişimi sınırlı tutun. Parolayı komut satırına yazmayın, korunmuş MySQL defaults dosyası kullanın.

    mysqldump --defaults-extra-file=/secure/mysql-backup.cnf --single-transaction --routines --triggers gaziustam > gaziustam.sql
    tar -czf storage-backup.tar.gz storage/app

Geri yükleme önce ayrı bir test veritabanında denenir.

    mysql --defaults-extra-file=/secure/mysql-restore.cnf gaziustam_restore < gaziustam.sql

Storage arşivini ayrı doğrulama dizinine çıkarın. Aynı APP_KEY ile test release'ini bu veritabanına bağlayın. Yayınlanmış içerik sayısı, taleplerin snapshot'ları, private ek indirme ve kuyruk kayıtları karşılaştırılır. İşlemden sonra test verileri kontrollü biçimde temizlenir.

Migration sonrası geri dönüş, önceki release kodunu ve uyumlu DB yedeğini birlikte gerektirebilir. Üretim veritabanında migrate:fresh kullanmayın. Gerçek günlük yedek servisi ve geri yükleme tatbikatı staging erişimiyle ayrıca doğrulanmalıdır.

## Smoke test ve izleme

- /up, /de, /en, /tr, blog ve portföy detayları 200 dönmeli.
- Bilinmeyen dil, taslak ve gelecekteki içerik 404 olmalı.
- Gerçek tarayıcıda CSRF ile teklif gönderimi tek kayıt ve referans üretmeli.
- Sales talebi görmeli, Editor taleplere ve eklere erişememeli.
- Worker SMTP test bildirimini işlemeli. Ayarlar ekranında başarısız iş kontrol edilmeli.
- APP_DEBUG kapalı, cookie Secure/HttpOnly ve private dosyaların doğrudan URL ile kapalı olduğu doğrulanmalı.
- Loglar ve failed_jobs için alarm kurulmalı. Talep gövdesi ve tokenlar harici hata izlemeye taşınmamalı.
- Lighthouse ve gerçek cihaz ölçümleri staging üzerinde alınmalı. Bu teslimde performans skoru iddiası yoktur.

Gerçek domain, SMTP, sunucu erişimi ve onaylı hukuki metinler sağlanmadığı için production/staging yayını yapılmadı.

