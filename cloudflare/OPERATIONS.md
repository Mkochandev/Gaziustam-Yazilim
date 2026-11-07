# Yayın, veri ve işletim

## Ücretsiz hedef

Yalnız Workers Free ve D1 Free kullanılır; kod ücretli abonelik, R2 bucket veya ücretli e-posta servisi oluşturmaz. Kotalar dolunca ücretsiz serviste hata olabilir. Cloudflare panelinde hesap planını ve Metrics/Row Metrics/Storage ekranlarını izleyin. Kota ve istek sayısı ziyaretçi sayısı ile aynı değildir.

## Sonraki yayın ve geri dönüş

`cloudflare/` dizininde `npm ci && npm run deploy`. Yerel config içindeki gerçek D1 ID'si korunmalıdır. Migration'lar eklemeli ve numaralıdır; `migrate fresh`, DB silme veya otomatik sıfırlama yoktur. Kod geri dönüşü Cloudflare Deployments/Versions ile yapılabilir; veritabanını eski koda geri çevirmek ayrıca değerlendirilir.

## Yedek

Yetkili bilgisayarda `npx wrangler d1 export DB --remote --output gaziustam.backup.sql`. SQL içerik, müşteri kayıtları ve BLOB ekleri barındırabilir; özel alanda saklayın. Export'u public/ içine veya GitHub'a koymayın. `.backup.sql` dosyaları ignore edilir. Geri yüklemeyi önce ayrı bir test D1 veritabanına deneyin; gerçek veritabanının üstüne körlemesine SQL çalıştırmayın.

D1'in kendi geri dönüş/Time Travel seçeneklerini planınıza göre panelden kontrol edin. Bu repoda uzak hesabınızda geri yükleme tatbikatı yapılmamıştır.

## Laravel verileri

Laravel dosyaları korunur fakat canlı Cloudflare uygulaması onları çalıştırmaz. Bilgisayarınızdaki SQLite/MySQL ile D1 farklı veritabanlarıdır. Bu sürüm Laravel kullanıcı şifrelerini veya içeriklerini otomatik taşımaz. Gerçek kayıtlar varsa önce kaynak export alın; çeviri/ana içerik/medya/form-snapshot eşlemesiyle ayrı import gerekir. Mevcut kaynağı silmeyin.

Yeni üretimde başlangıç dilleri ve form eklenir; demo içerikleri varsayılan olarak eklenmez. Owner yeni kurulumda CLI ile oluşturulur. Gerçek gönderimler e-posta servisine bağlı değildir; yalnızca commit tamamlanınca başarı görünür.

## Aktif olmayan entegrasyonlar

E-posta bildirimi ve otomatik veri saklama süresi temizliği bu sürümde etkin değildir. İç kayıtlar panelde kalır; silme/saklama politikasını işletmenizle netleştirin. AI metin üretimi ve otomatik paylaşım sistemi yerine tokenlı içerik API'si sağlanmıştır. Harici sağlayıcı seçimi ayrı kurulumdur.

## Güvenlik sınırları

Admin/POST/private dosyalar paylaşımlı cache'e alınmaz. Cloudflare cache kuralıyla bu yolları zorla cache'lemeyin. Session secret'ı döndürmek CSRF tokenlarını geçersiz kılar; aktif oturumları ayrıca silmek gerekebilir. Owner hesabı kapanmadan önce ikinci owner bulunduğunu doğrulayın. Şifre unutma e-postası bağlı değildir; mevcut owner yeni hesap açabilir veya kontrollü CLI müdahalesi gerekir.

Prod için otomatik public signup veya setup/backdoor endpoint'i yoktur. Yerel QA hesapları ve veritabanı bu repoya dahil değildir.
