# GaziUstam — Laravel uygulama planı ve Codex görevleri

> Durum: 7 Kasım 2026 yerel kabul sonuçları işlendi. İşaretli görevler uygulandı ve doğrulandı. Staging/production kontrolleri açık bırakıldı. Ayrıntılar docs/VERIFICATION.md içindedir.
> Marka: gaziustam / GaziUstam. Alan: yazılım, mobil uygulama, web, e-ticaret, oyun, AI ve otomasyon ajansı.
> Öncelikli pazar: Almanya ve Avusturya; kapsam: Avrupa. Geliştirme ekibi Türkiye'de.
> Karar: İlk beğenilen tasarım korunacak; üretim uygulaması Laravel ile geliştirilecek.

## 1. Codex'e verilecek başlangıç talimatı

Bu dosyayı projenin köküne `TASKS.md` olarak koy ve aşağıdaki talimatla başla:

```text
TASKS.md dosyasını bütünüyle oku. GaziUstam için Laravel tabanlı, sunucuda render edilen, çok dilli bir ajans sitesi geliştir. Tasarımda bu belgedeki ilk tasarımı ve görsel tarifini esas al. Next.js/React uygulaması kurma. Mevcut tasarım kaynakları varsa önce incele; HTML/CSS ve görsel varlıkları Blade bileşenlerine taşı. Kaynak erişilemiyorsa bunu belirt ve verilen tasarım tarifini uygula; görmediğin kaynağı birebir kopyaladığını iddia etme.

Önce depoyu ve çalıştırma ortamını incele. Belgede karara bağlanmış rutin konuları yeniden sorma. Çalışan parçaları koru, sırları repoya yazma. Eksik harici servislerde sahte başarı gösterme; lokal test sürücüsü ve açık kurulum notu bırak. Aşamaları bağımlılık sırasıyla uygula. Her aşamanın kabul kriterlerini doğrula, sadece geçen görevleri işaretle. İşi yalnızca statik ekran veya plan aşamasında bırakma: veritabanı, admin yetkileri, dil filtreleme ve teklif kaydı gerçekten çalışsın.

İlk hedef: Faz 0–4 ile ilk tasarıma uygun ana sayfa, gerçek çok dilli haber akışı ve panelden yönetilen teklif formu. Sonra portföy, API ve yayın hazırlığını tamamla. Her aşama sonunda değişiklikleri, testleri ve kalan işleri kısa raporla. Test/çalıştırma yapılamıyorsa nedenini dürüstçe belirt.
```

## 2. Değiştirilmeyecek ürün kararları

- İlan pazaryeri, freelancer üyeliği, açık teklif yarışı yok. Site bir ajansın hizmet ve müşteri talebi toplama sitesi.
- Müşteri hesap açmadan proje talebi gönderebilecek. Admin paneli ekip için olacak.
- Ana sayfanın bölümleri, yerleşimi ve animasyonları koddan değiştirilecek. Genel amaçlı sayfa oluşturucu yapılmayacak.
- Haber/blog içerikleri ve çevirileri panelden yönetilecek.
- Portföy ve proje çevirileri panelden yönetilecek.
- Teklif formunun alanları, sıralaması, seçenekleri ve metinleri panelden değiştirilebilecek.
- Form gönderimleri gerçek olarak kaydedilecek; mevcut prototipteki yalnızca önizleme davranışı kaldırılacak.
- Admin arayüzünün başlangıç dili Türkçe; ziyaretçi arayüzünün varsayılan dili Almanca.
- Önce DE/EN/TR eksiksiz hazırlanacak. Avrupa dilleri merkezi bir dil kayıt listesinde bulunacak; arayüz çevirileri hazır olanlar etkinleştirilecek. Boş sayfa üreten dil seçeneği göstermeyeceğiz.
- Bir içeriğin çevirisi, aynı ana içeriğin ayrı dil sürümü olacak. Aynı yazıyı bağımsız, ilişkisiz kopyalara dönüştürme.
- Çeviri otomatik üretilmiş sayılmaz. Metin girilmeden veya API'den gelmeden yayınlanmış çeviri oluşturma.
- Gerçek olmayan müşteri, iş sonucu, puan, referans, ekip büyüklüğü veya sertifika iddiası üretme. Demo projeleri açıkça konsept olarak etiketle.

## 3. Mimari ve paket seçimi

Tercih edilen yapı:

| Katman | Karar |
|---|---|
| Uygulama | Kurulum anında desteklenen, paketlerle uyumlu kararlı Laravel/PHP sürümü |
| Ziyaretçi arayüzü | Blade, Vite, Tailwind CSS veya mevcut tasarımdan aktarılan düzenli CSS |
| Küçük etkileşimler | Alpine.js; gerçekten gerekirse Livewire |
| Yönetim paneli | Filament; proje ile uyumlu kararlı sürüm |
| Veritabanı | MySQL; üretime yakın testler için de MySQL |
| Kuyruk | Başlangıçta database queue, sonradan Redis'e geçilebilir |
| Önbellek | Başlangıçta database/file; sürücü yapılandırılabilir |
| Medya | Laravel Filesystem; public proje görselleri ve private talep ekleri ayrı diskler |
| İçerik entegrasyonu | Laravel API, kapsamlı ve iptal edilebilir tokenlar; Sanctum tercih edilir |
| Hosting | PHP destekli hosting / VPS / Laravel uyumlu yönetilen servis; Cloudflare DNS/CDN önde |

Kurulumda sürüm uyumluluğunu resmi dokümanlardan doğrula, seçimi `README.md` içinde kaydet ve kilit dosyalarını commit et. Livewire kullanan panelde Alpine'ı ikinci kez başlatma. Ön yüzü admin panelinin varsayılan görünümüne benzetme; kamuya açık site özel tasarım olacak.

Cloudflare Pages'e Laravel dosyalarını yükleyerek tam uygulamayı çalıştırmayı planlama. Üretim için PHP çalışma ortamı, veritabanı, scheduler ve queue worker gerekecek. AI ile metin üretimini kullanıcı isteği içinde ağır bir senkron işlem olarak çalıştırma.

## 4. Tasarım referansı ve görsel hedef

Birinci ve seçilen referans: https://gaziustam.recentcrayon1.chatgpt.site

İkinci tasarım karşılaştırma için: https://gaziustam-atelier.recentcrayon1.chatgpt.site

İkinci örneğin bordo/krem/serif tasarımını ana tasarım olarak uygulama. Seçilen yön birinci tasarımın kırık beyaz, siyah ve elektrik mavisi olan modern stüdyo kimliği.

### 4.1 Görsel sistem

- Zemin yaklaşık `#f7f7f2`; metin `#151515`; vurgu `#3155ff` / `#3355ff`; ince ayraç `#ddded7`.
- Büyük, sıkı harf aralıklı sans serif başlıklar. Lisanslı yerel font veya sistem fontu kullan; gereksiz uzaktan font isteği ekleme.
- Masaüstünde en fazla yaklaşık 1440–1536 px içerik genişliği, 56–64 px yatay boşluk. Mobilde 20–24 px.
- Geniş boşluklar, ince çizgiler, büyük tipografi ve özenli ürün sunumları. Her şeyi küçük gölgeli kartlara doldurma.
- Birincil butonlar siyah, yuvarlatılmış; hover ve aktif durumlarda mavi. İkincil butonlarda ince kenarlık.
- Logo: gaziustam yazısı ve mavi sekiz kollu yıldız. Yıldız tüm cihazlarda aynı SVG olmalı. Unicode yıldız/emoji karakteri kullanma. Menü ve diğer simgelerde de SVG tercih et.
- Büyük mavi-krom soyut heykel ana görseli ve mevcut proje görselleri kaynak varsa korunacak. Yeni görsel gerekirse hakları uygun varlık kullan; dosya bulunamadığında kırık resim bırakma.
- Arayüz Almanca uzun metinlerde, Türkçede ve dar mobil genişlikte taşmamalı. Sabit piksel yüksekliği ile başlık kırpma.
- Hareketler ölçülü: 150–300 ms hover, hafif görsel yakınlaşması. `prefers-reduced-motion` desteği. Scroll kilitleyen efektler ve ağır intro animasyonu yok.

### 4.2 Ana sayfa bölümleri, sırayla

1. Sabit üst menü: logo, hizmetler, projeler, blog/haberler, dil seçici, proje başlat CTA. Mobilde erişilebilir açılır menü.
2. Hero: Türkiye–Avrupa küçük üst etiketi; büyük iki satırlı başlık; ikinci satır mavi; sağda kısa açıklama ve teklif CTA. Almanca yön: “Digitale Lösungen. Türkische Expertise.” Türkçe yön: “Usta işi yazılım. Türkiye’den Avrupa’ya.”
3. Geniş mavi/krom ana görsel, üzerinde az miktarda stüdyo metni ve projelere kaydıran bağlantı.
4. Hizmet disiplinleri şeridi, aralarda SVG yıldızlar.
5. Portföy: ilk proje geniş, diğerleri iki sütun; kategori filtreleri; gerçek proje kaydı yoksa açıkça konsept etiketi. Kart detaya gider.
6. Hizmetler: solda büyük başlık ve SVG yıldız; sağda numaralı altı hizmet satırı. Satır tıklanınca teklif formunda ilgili hizmet seçilir.
7. Süreç: projenizi anlatın, görüşelim, geliştirelim/teslim edelim şeklinde üç sade adım.
8. Blog/haber seçkisi: mevcut dilde yayınlanmış en yeni üç içerik. Yabancı dildeki yazıyı sessizce yerel içerik gibi sunma.
9. Teklif formu: mevcut tasarım dili içinde, veritabanındaki aktif form sürümünden render edilir.
10. Büyük gaziustam footer yazısı, SVG yıldız, gezinme ve hukuki sayfa bağlantıları.

Ana sayfada bölüm tasarımı kodda; içerik kaynakları gerektiğinde sorgulanır. Proje sıralaması ve öne çıkarma panelden değişebilir. Hero ve bölüm yerleşimini değiştirmek için admin sayfa editörü ekleme.

### 4.3 Haber/blog liste ve detay tasarımı

- Liste üstünde büyük “Journal / Insights” tarzı yerelleştirilmiş başlık, kısa açıklama.
- Haber / makale türü, kategori ve arama filtreleri; türler tek içerik sistemi içinde tutulur.
- Görünür tek tuş: “Yalnızca Türkçe içerikler” / “Nur Inhalte auf Deutsch”. Aktif dil adı dinamik gelir. Varsayılan açık.
- Bu düğme otomatik çeviri başlatmaz; o dilde yayınlanmış sürümü olan içerikleri filtreler.
- Kapalıyken diğer dillerdeki içerikler de listelenebilir; kart üzerinde gerçek içerik dili rozeti açıkça görünür.
- Seçenek URL'de saklanır: ör. `/de/blog?language=all&type=news`. Varsayılan `language=current`; mevcut kategori/arama korunur. Sayfa sayacı filtre değişince 1'e döner.
- Aktif dilde çeviri varsa `all` görünümünde de o sürüm tercih edilir. Yoksa içerik kaydının yayınlanmış kaynak dili sürümü; o da yoksa etkin dil önceliği DE, EN, TR, ardından dil sırası. İçerik başına tek kart.
- Başka dildeki karta tıklama o sürümün gerçek dil URL'sine gider; Almanca URL altında İngilizce metin gösterme.
- Kart: kapak/özenli metin görseli, tür, kategori, tarih, içerik dili, başlık, özet ve okuma bağlantısı.
- Detay: yaklaşık 720–800 px okunabilir metin alanı; başlık, özet, tarih, kapak, yazar (varsa), zengin içerik; mevcut çevirilere bağlantılar.
- Çeviri bulunmayan dilin detay bağlantısını üretme. Dil seçimi sırasında “Bu yazının bu dilde çevirisi henüz yok” açıklaması ve hedef dilin liste sayfası bağlantısı göster.
- Boş sonuç, görselsiz içerik ve yükleme/hata durumları tasarlanacak. “Hepsini göster” ile dil filtresi kapatılabilir.

### 4.4 Portföy liste ve detay tasarımı

- Liste: kategori sekmeleri, büyük proje kartları, medya odaklı sunum; aynı görsel kimlik.
- Detay: proje adı, kısa amaç, kapak, problem, çözüm, teslim edilenler, teknoloji etiketleri, galeri, varsa izinli canlı bağlantı, teklif CTA.
- Müşteri adı/sonuç ölçümleri zorunlu değil; gizli projelerde anonim başlık kullanılabilir.
- Yerelleştirme blog ile aynı ana kayıt + dil sürümü yaklaşımı; listede varsayılan yalnız mevcut dilde yayınlanmış projeler.

### 4.5 Admin tasarım tarifi

- Türkçe panel, açık zemin, okunabilir tablolar, mavi vurgu. 240–260 px sol menü; üstte arama ve hesap menüsü.
- Menü: Genel Bakış, Talepler, Blog/Haberler, Portföy, Medya, Teklif Formu, Diller, API Erişimleri, Kullanıcılar, Ayarlar.
- Genel bakışta yeni talepler, görüşülen talepler, taslak yazılar, eksik çeviriler. Gerçek kayıtlardan hesapla.
- İçerik listesinde başlık, tür, durum, yayın tarihi ve dil rozetleri. DE/EN/TR gibi rozetlerde taslak/yayında/eksik ayrımı.
- İçerik editöründe solda başlık/özet/gövde, sağda yayın durumu/kapak/kategori/SEO; üstte dil sekmeleri ve “Dil sürümü ekle”. Küçük ekranlarda alt alta.
- Talep detayında solda müşteri ve tüm form cevapları, sağda durum/sorumlu/notlar/geçmiş. Notlar müşteriye görünmez.
- Form editörü: solda alan listesi, ortada alan ayarları, sağda canlı önizleme; mobilde sekmeler. Sıra değişimi sürükleme yanında yukarı/aşağı butonlarıyla da mümkün.
- Kaydet, önizle ve yayınla eylemleri ayrışsın. Kirli formdan ayrılmada veri kaybı uyarısı; erişilebilir doğrulama mesajları.

## 5. Çok dillilik modeli ve kesin davranışlar

Arayüz çevirileri ile içerik çevirileri ayrı sistemlerdir. Laravel dil dosyaları menü/buton/validasyon gibi arayüz metinlerini; veritabanı ise haber/proje/form metinlerini tutar.

- `locales`: `code` (BCP 47 uyumlu, ör. de/en/tr/pt-PT), `native_name`, `direction`, `is_active`, `sort_order`.
- `/` varsayılan `/de` adresine yönlenir. Aktif olmayan/bilinmeyen dil 404 olur. Dil seçimi açık URL ile yapılır; IP'ye göre zorunlu yönlendirme yok.
- Her içerik çevirisinin bağımsız taslak/yayın durumu ve yayın tarihi vardır.
- Yayınlanabilirlik: ana kayıt arşivde/silinmiş değil + dil etkin + çeviri published + `published_at <= now`.
- Gelecek tarihli sürüm yayın zamanı gelene kadar sorgulara ve sitemap'e girmez. İçerik yayınlandığında ilgili önbellek yenilenir.
- `slug` dil içinde benzersizdir. Taslak çevirilerin slug'ı da rezerve edilir; eski slug kayıtları 301 yönlendirmesi için korunur.
- Yeni dil sürümü boş veya diğer dilden kopyalanmış taslak olarak açılabilir. Kopyalanmış metin otomatik yayınlanamaz.
- Panelde DE yayında, EN taslak, TR eksik gibi görünüm desteklenir.
- Tüm Avrupa dillerini destekleyebilen yapı kur; eksik arayüz çevirilerini tamamlanmış sayma. Mevcut 44 dil dosyası elde varsa doğrula/taşı; kalitesi kontrol edilmeyen metinleri inceleme listesine al.
- Form dil sürümü tamamlanmamışsa o dil etkinleştirilemez veya form için açık tanımlı fallback uygulanır. Varsayılan tercih: etkinleştirmeyi engelle, eksik alanları göster.
- `html lang` ve gerektiğinde `dir` doğru atanır; dil kodu ve BCP 47 karşılığı merkezi tutulur.

**Örnek kabul senaryosu:** A yazısı DE ve TR yayında, EN taslak; B yazısı yalnız EN yayında. `/de/blog` varsayılanında A görünür, B görünmez. Düğme kapatılınca A Almanca ve B İngilizce rozetiyle görünür. A iki kez görünmez. EN taslağı hiçbir public listede/detailde/sitemap'te görünmez.

## 6. Blog/haber ve portföy veri modeli

İlişkisel tablolar tercih et; bütün dilleri tek büyük JSON alana doldurma.

| Tablo | Önemli alanlar ve ilişkiler |
|---|---|
| users | ad, e-posta, şifre, rol, aktiflik; herkese açık kayıt yok |
| posts | id, type: article/news, source_locale, category_id, cover_media_id, author_id, archived_at, timestamps, soft delete |
| post_translations | post_id, locale, title, slug, excerpt, body, cover_alt, meta_title, meta_description, status, published_at; unique(post_id,locale), unique(locale,slug) |
| categories / category_translations | code, kapsam; locale, name, slug |
| projects | source_locale, category_id, cover_media_id, client_display_name opsiyonel, project_url, is_concept, featured, sort_order, archived_at, soft delete |
| project_translations | project_id, locale, title, slug, summary, problem, solution, deliverables, cover_alt, SEO, status, published_at; dil bazlı benzersizlik |
| project_media | proje-görsel ilişkisi, sıra; alt yazıları için ayrı dil tablosu |
| media | disk, path, MIME, boyut, genişlik/yükseklik, uploader; public/private ayrımı |
| slug_redirects | entity_type, entity_id, locale, old_slug; eski yol çakışmalarını engelle |
| activity_logs | actor, action, subject, timestamp, güvenli meta; sır ve talep gövdesini loglama |

Zengin metin editörü başlık, paragraf, liste, bağlantı ve resim desteklesin. Sunucu tarafında izin verilen HTML etiketleri/özellikleriyle sanitize et. Script/iframe/event attribute eklenemesin. `javascript:` bağlantıları kabul edilmesin. API girişleri de aynı servisten geçsin. Yazı içinde üçüncü tarafa ait metni kaynaksız kopyalama otomasyonu kurma.

Portföy panelinde kapak, galeri sırası, hizmet kategorisi, teknoloji etiketleri, öne çıkarma, konsept etiketi, dil sürümleri ve önizleme bulunacak. Müşteri gizliliği için isim/URL opsiyonel. Silinen medya kullanımda ise uyarı; kayıt bağlı dosyaları sessizce boşa düşürmesin.

## 7. Teklif formu oluşturucu

Bu modül genel bir site oluşturucu değildir. Yalnız teklif formunun yapılandırılması içindir.

### 7.1 Alanlar ve sınırlar

- Destek: kısa metin, uzun metin, e-posta, telefon, select, radio, çoklu seçim, tarih, onay kutusu, dosya eki.
- Her alan: değişmeyen `key`, tür, sıra, genişlik (tam/yarım), zorunluluk, aktiflik, kontrollü validasyon ayarları.
- Her dil: label, placeholder, yardım metni, kontrollü hata metinleri. Seçeneklerin değişmeyen value'su ve yerelleştirilmiş etiketi.
- Editörden keyfi PHP, JavaScript, SQL, HTML, regex veya Laravel validation string çalıştırılamaz. Ayarlar allowlist üzerinden oluşturulur.
- E-posta alanı gibi çekirdek alanlar korunur; en az `contact_name`, `contact_email`, `message` etkin ve uygun türde kalmalıdır. Kalıcı key/tür değişimi yerine yeni alan oluşturulur.
- “Hizmet” alanı sabit hizmet kodlarıyla eşlenir. Ana sayfadaki hizmet CTA'sı key üzerinden seçim yapar; label'a veya dizi indeksine bağlanmaz.
- İhtiyaç olmayan koşullu alan motoru, hesaplama alanı ve çok adımlı wizard ilk sürümde yok.
- Zorunlu veri işleme bilgilendirmesi varsa metni ve linki yerelleştirilir. Pazarlama izni ayrı ve isteğe bağlıdır; önceden işaretli olmaz. Hukuki metni yayına hazır profesyonel görüş gibi uydurma.

Varsayılan alanlar: ad, e-posta, şirket (opsiyonel), telefon (opsiyonel), hizmet, bütçe, tahmini başlangıç, proje açıklaması, dosya eki (opsiyonel). Bütçeler: henüz belli değil, 0–5.000 €, 5.000–15.000 €, 15.000–30.000 €, 30.000 € üzeri.

### 7.2 Sürümleme: eski talepler bozulmamalı

- `forms`: `key=project_enquiry`, aktif yayınlanmış sürüm referansı.
- `form_versions`: form_id, version_number, status: draft/published/retired, schema JSON, schema_version, created_by, published_at. Yayınlanmış sürüm değişmez.
- Alanları ve çevirileri JSON şemasında tutmak burada kabul edilir; içerik çevirilerinden farklı olarak form tek sürümlü bir sözleşmedir. Şema doğrulanmadan yayınlanamaz.
- Düzenleme yeni taslak üretir; yayınlama aktif sürümü atomik değiştirir. Açık form version_id ile gönderilir.
- İstemcinin gönderdiği field/schema tanımına güvenme. Sunucu ilgili yayınlanmış sürümden kuralları türetir.
- Eski açık form sürümü için 24 saat geçiş süresi tanı; daha eskiyse kullanıcının girdisini kaybetmeden formu yenilemesini iste. Emekliye alınan sürümün geçmiş kayıtları okunabilir.
- Talepte version_id ve gönderim anındaki yerelleştirilmiş şema/etiket/seçenek snapshot'ı tutulur. Alan kaldırıldığında veya label değiştiğinde eski cevaplar doğru görünür.
- Bilinmeyen alanlar, gizli/kapalı alanlar ve izin verilmeyen option value'ları kabul edilmez. Sistem alanları mass assignment ile değiştirilemez.

### 7.3 Gerçek talep kaydı

- Başarı yalnız kalıcı veritabanı kaydı tamamlandıktan sonra gösterilir. Başarı ekranında tahmin edilemeyen referans numarası ve sonraki adım metni olur; panel linki yok.
- `enquiries`: public_reference (rastgele), form_version_id, locale, contact_name, contact_email, company opsiyonel, service_code, budget_code, status, assigned_to, submitted_at, schema_snapshot, idempotency_key.
- `enquiry_answers`: enquiry_id, field_key, answer JSON. Tekrarlanan çekirdek alan değerleri transaction içinde tutarlı yazılır.
- `enquiry_attachments`: enquiry_id, private disk/path, original_name, MIME, size. İndirme yalnız yetkili panel kullanıcısına açık.
- `enquiry_notes`: enquiry_id, author_id, body, timestamps; yalnız ekip görür.
- `enquiry_status_histories`: eski/yeni durum, kullanıcı, zaman.
- Çift tıklama ve ağ tekrarında idempotency anahtarı aynı kaydı döndürür. Birden fazla talep ve bildirim oluşmaz.
- Kayıt transaction ile tamamlanır; dosya/DB hatalarında yetim dosyalar temizlenir. Bildirim işi commit sonrası kuyruğa girer. Mail servisi bozulsa da kaydedilmiş talep kaybolmaz, müşteriye tekrar gönder denmez.
- Spam için honeypot + rate limit; üretimde Turnstile seçeneği. CAPTCHA sırrı yalnız sunucuda, doğrulama hata durumları açık.
- Dosya varsayılanı PDF/JPG/PNG/WebP, dosya başı 5 MB, en fazla 3 ek. MIME ve uzantı birlikte doğrulanır; executable/SVG/HTML/ZIP ilk sürümde reddedilir. Rastgele dosya adı, private storage, indirmede güvenli başlıklar. İçerik taraması eklenecekse ayrıca yapılandır.
- Başlangıçta retention süresi ayarlanabilir olsun; iş ihtiyacı olmadan süresiz veri biriktirme. Saklama/pazarlama metinlerini kullanıcıya inceleme görevi olarak bırak.

## 8. Talepler paneli ve yetkiler

Durumlar: Yeni → İnceleniyor → Görüşülüyor → Teklif gönderildi → Kabul edildi / Kapatıldı. İptal ve spam ayrı seçenek olabilir. Geçişler kaydedilir; geçmiş kaybolmaz.

Tablo: referans, müşteri/şirket, hizmet, bütçe, dil, durum, sorumlu, tarih. Filtreler: durum, hizmet, dil, sorumlu, tarih aralığı; arama: isim/e-posta/referans. Varsayılan en yeni ilk.

Detay: bütün cevaplar orijinal gönderim dilindeki etiketlerle; ekler; iç notlar; durum/sorumlu; geçmiş. İç notları public API/başarı yanıtına dahil etme. Bildirim e-postasında tüm proje açıklaması yerine referans ve panel bağlantısı tercih et.

| Rol | Yetki |
|---|---|
| Owner | tüm modüller, kullanıcı/rol ve API erişimi |
| Editor | haber, portföy, medya ve çeviri; müşteri talepleri/API tokenları yok |
| Sales | talepler, iç notlar, durum/sorumlu; kullanıcı ve içerik yayınlama yok |

Yetki yalnız menü gizlemekle sınırlı değil; Policy/Gate ile sorgu, route, action ve indirme düzeyinde uygulanır. Hesap oluşturma yalnız Owner tarafından; sabit varsayılan üretim şifresi yok. Lokal demo hesapları sadece local/testing ortamında.

## 9. Blog API'si ve otomasyon

API hazırlansın; harici servise gerçek otomasyon bağlama bu görevin zorunlu parçası değil. Örnek istemci ve istekler sağlanacak.

- `/api/v1/posts`: ana kayıt oluşturma/güncelleme.
- `/api/v1/posts/{id}/translations/{locale}`: çeviri upsert; aynı ana kayda bağlanır.
- `/api/v1/posts/{id}/translations/{locale}/publish`: ayrı yayınlama eylemi; yayınlama kapsamı gerektirir.
- Varsayılan yeni içerik `draft`; tokenın publish kapsamı yoksa published isteği 403 olur.
- Okuma/yazma/yayınlama kapsamları ayrılır. Tokenlar hash'lenir, yalnız oluşturulurken bir kez gösterilir; son kullanım ve iptal desteği.
- Harici sistemde aynı yazı tekrar geldiğinde çoğalmaması için unique(provider,external_id) kullan. İdempotent çeviri güncellemesi.
- Payload: başlık, özet, sanitized gövde, tür, kategori, dil, kaynak URL, SEO, kapak media_id. Rastgele uzak URL'den sunucuya görsel çekme ilk sürümde kapalı; SSRF açığı oluşturma.
- Hatalar tutarlı JSON: alan hatalarında 422, auth 401/403, çakışmada 409, limitte 429.
- API panelden yapılan işlemle aynı servis/validasyon/yetki ve yayın kurallarını kullanır. Taslaklar public endpoint'e sızmaz.
- API ile yayın için yalnız özgün/lisanslı içerik akışı hedefle. Yoğun otomatik içerik üretiminin trafik artışını garanti ettiğini iddia etme.
- İnternetteki rastgele bir AI servisine varsayılan anahtar bağlama. `.env.example` ve örnek curl/istemci belgeleri yeterli; sırları örneğe yazma.

## 10. Rotalar ve SEO

| Rota | Davranış |
|---|---|
| `/` | `/de` yönlendirmesi |
| `/{locale}` | ana sayfa |
| `/{locale}/blog` | haber/makale listesi ve dil filtresi |
| `/{locale}/blog/{slug}` | yalnız yayınlanmış gerçek dil sürümü |
| `/{locale}/projects` | portföy listesi |
| `/{locale}/projects/{slug}` | yayınlanmış proje dil sürümü |
| `/{locale}/enquiries` POST | form sürümüne göre doğrula ve kaydet |
| `/admin` | oturum ve rol kontrollü panel |
| `/sitemap.xml` | yalnız etkin dilde yayınlanmış canonical sayfalar |
| `/robots.txt` | public tarama kuralları; admin için ayrıca auth |

- Canonical her gerçek çevirinin kendi URL'si. `hreflang` yalnız yayınlanmış mevcut çeviriler arasında, karşılıklı üretilir. `x-default` kullanılacaksa tutarlı varsayılan sayfaya işaret eder.
- Taslak önizleme signed + süreli + yetki kontrollü, `noindex`, shared cache dışı. URL'yi bilmek taslağı okumaya yetmez.
- Arama ve filtre query sayfaları için tutarlı canonical/noindex politikası belirle; sınırsız indekslenecek query kombinasyonu üretme.
- Slug değişikliği 301, silinmiş/yayından kaldırılmış içerik uygun 404; otomatik alakasız anasayfa yönlendirmesi yok.
- Article/NewsArticle ve Organization yapılandırılmış veri gerçek içerikle uyumlu; sahte review/rating yok.
- Görseller boyut bilgili, responsive, WebP/AVIF mümkünse; hero öncelikli, aşağıdaki görseller lazy. Yazı içindeki alt metinler yerelleştirilir.
- Kamuya açık içerik cache anahtarları locale + sorgu parametrelerini içerir. Admin, oturum, POST, talepler ve private dosyalar Cloudflare paylaşımlı cache'e girmez. Form CSRF tokenları statik paylaşımlı HTML önbelleğinden yanlış sunulmaz.

## 11. Kod organizasyonu önerisi

```text
app/Models/{Post,PostTranslation,Project,ProjectTranslation,FormVersion,Enquiry,...}
app/Services/Content/{PostService,PublicationService,TranslationService}
app/Services/Forms/{FormSchemaValidator,FormRendererData,EnquiryService}
app/Http/Controllers/Public/{HomeController,BlogController,ProjectController,EnquiryController}
app/Http/Controllers/Api/V1/...
app/Http/Requests/...
app/Policies/...
app/Filament/Resources/...
app/Jobs/{SendEnquiryNotification,...}
resources/views/layouts/public.blade.php
resources/views/components/{header,footer,studio-star,project-card,post-card,form-field}.blade.php
resources/views/{home,blog/index,blog/show,projects/index,projects/show}.blade.php
lang/{de,en,tr}/...
database/{migrations,factories,seeders}/...
tests/Feature/{Localization,Content,Enquiries,Admin,Api}/...
docs/{DESIGN,DEPLOYMENT,API,CONTENT_GUIDE}.md
```

Bu klasörler öneridir; çalışan proje standardı varsa ona uy. Controller'ları dev iş mantığı dosyalarına çevirmeden servis ve policy sorumluluklarını ayır. Gereksiz repository soyutlamaları ekleme.

## 12. Sıralı uygulama görevleri

### Faz 0 — Kaynak inceleme ve temel kurulum

- [x] Mevcut repo, tasarım, görsel varlıklar, prototip form ve dil kaynaklarını incele; taşınacakları listele.
- [x] Laravel/PHP/Filament uyumlu sürümleri seç, lock dosyaları ve `.env.example` oluştur.
- [x] MySQL bağlantısı, migration, queue, storage ve Vite çalışsın.
- [x] README: kurulum/çalıştırma/test, admin oluşturma; `DECISIONS.md`: mimari kararlar.
- [x] Lokal seed: DE/EN/TR, altı hizmet, demo olduğu açık içerikler. Üretime sahte müşteri verisi basma.

Kabul: temiz checkout'tan belgelenmiş komutlarla uygulama açılır; gerçek sır veya sabit üretim admin şifresi yok.

### Faz 1 — Görsel temel ve ana sayfa

- [x] Tasarım tokenları, Blade layout, header/footer, SVG yıldız bileşeni.
- [x] İlk tasarımın bölüm sırası, tipografi, mavi hero ve portföy sunumları.
- [x] DE/EN/TR dil route'ları ve seçici, mobil menü, hizmetten forma seçim.
- [x] Blog, portföy ve iletişim bağlantıları gerçek route'a gider; kırık placeholder link yok.
- [x] 360/390/768/1280/1440 px kontrolleri, klavye odağı ve azaltılmış hareket.

Kabul: seçilen ilk tasarımın görsel karakteri korunur; mobilde emoji yıldız ve yatay taşma yok.

### Faz 2 — Admin, içerik ve çeviriler

- [x] Admin giriş/çıkış, roller ve policies.
- [x] Post/translation/category/media modelleri, migration ve Filament kaynakları.
- [x] Haber/makale türü, dil sürümü ekleme, zengin metin, kapak, SEO, taslak/yayın/planlama.
- [x] Çeviri matrisi, mevcut çeviriler arası geçiş, signed taslak önizleme.
- [x] Blog liste/detay, current/all dil düğmesi, kategori/tür/arama ve pagination.
- [x] Yayın sorguları ve cache temizliği ortak servislerden.

Kabul: bölüm 5'teki A/B dil senaryosu, panelden eklenen gerçek verilerle çalışır. Taslaklar ve gelecekteki sürümler sızmaz.

### Faz 3 — Form editörü

- [x] Sürümlü JSON şeması, allowlist validatörü ve varsayılan proje formu.
- [x] Alan ekleme/sıra/gizleme, çekirdek alan koruması, çeviri ve seçenek editörü.
- [x] Taslak–önizleme–yayınlama; eksik dil ve geçersiz şema kontrolleri.
- [x] Public Blade renderer, sunucu doğrulama kuralları ve CSRF.
- [x] Erişilebilir label/hata ilişkileri, alan bazlı hata ve girdileri koruma.

Kabul: panelden label/alan sırası değiştirilince taslak önizlemede görülür; yayınlanınca public forma yansır; yayınlanmış eski sürüm değişmez.

### Faz 4 — Gerçek talepler ve ekip akışı

- [x] Transaction içinde talep, cevaplar, snapshot ve ekler kaydı.
- [x] Idempotency, spam limiti, honeypot ve yapılandırılabilir Turnstile.
- [x] Yetkili talep listesi/detayı, filtreler, notlar, durum/sorumlu ve geçmiş.
- [x] Commit sonrası kuyruk bildirimi, başarısız işler ve tekrar deneme yönetimi.
- [x] Private ek indirme ve sahiplik/yetki kontrolleri.

Kabul: public form gönderimi panelde tek kayıt olur. Form düzenlendikten sonra eski talep etiketleri/cevapları bozulmaz. Mail arızası kaydı kaybettirmez.

### Faz 5 — Portföy yönetimi

- [x] Proje/çeviri/galeri modelleri ve panel CRUD.
- [x] Kategori, sıralama, öne çıkarma, konsept, URL ve SEO.
- [x] Ana sayfa seçkisi ve portföy liste/detay veri bağlantısı.
- [x] Dil görünürlüğü, görsel optimizasyonu ve galeri erişilebilirliği.

Kabul: admin panelden eklenen yayınlanmış proje doğru dilde ana sayfada ve portföyde görünür; taslak görünmez.

### Faz 6 — API ve SEO

- [x] Kapsamlı token oluşturma/iptal ve içerik API'si.
- [x] Draft varsayılanı, ayrı publish yetkisi, external_id idempotency.
- [x] Payload örnekleri ve kurulum gerektirmeyen test istemcisi.
- [x] Canonical, hreflang, sitemap, slug 301 ve structured data.
- [x] Önbellek anahtarları ve yayın zamanı/silme güncellemeleri.

Kabul: API taslağı panelde görülür; publish yetkisi olmayan token yayınlayamaz; aynı kaynak yazı tekrar gönderilince çoğalmaz.

### Faz 7 — Üretime hazırlık ve teslim

- [ ] MySQL üzerinde kritik feature testleri ve staging smoke testleri.
- [x] Queue worker/scheduler, günlük backup ve restore denemesi belgeleri.
- [ ] HTTPS, production debug kapalı, güvenli session/cookie, private dosya kontrolü.
- [x] CDN cache istisnaları, hata izleme, yapılandırma ve kapasite notları.
- [x] Masaüstü/mobil ekran görüntüleriyle görsel QA raporu.
- [x] README + DEPLOYMENT + API + CONTENT_GUIDE ve tamamlanan görev listesi.
- [x] Gerçek SMTP/domain/token gibi kullanıcıya ait eksikler açıkça teslim raporunda; bağlı değilken bağlı denmez.

## 13. Anlamlı test matrisi

| Test | Beklenen |
|---|---|
| DE yayın, EN taslak çeviri | EN detail ve listede görünmez; DE görünür |
| Current/all düğmesi | filtre çalışır, gerçek dil rozeti, tek ana içerik başına tek kart |
| Dil + kategori + sayfalama | parametreler korunur, filtre değişince sayfa 1 |
| Yayın tarihi gelecekte | public/sitemap dışı; zaman gelince doğru görünür |
| Slug güncelleme | eski URL yeni canonical'a 301; başka kayıtla çakışma engellenir |
| Editor rolü | içerik yönetir, talep ve private eklere 403 |
| Public admin erişimi | login zorunlu; kaynak URL ile yetki atlanamaz |
| Yeni talep | tek kayıt, cevap/snapshot eksiksiz, görünür referans |
| Aynı gönderim tekrar | ikinci kayıt ve ikinci bildirim oluşmaz |
| Şema sürümü/alan manipülasyonu | izin verilmeyen alan/value reddedilir |
| Form sonrası alan silme | eski talepte orijinal label ve cevap korunur |
| Eksik zorunlu alan | yerel dilde 422/hata; girişler kaybolmaz |
| Mail servisi hatası | kayıt kalır, başarısız bildirim yeniden denenebilir |
| Yetkisiz dosya indirme | 403/404, public storage URL'si yok |
| Sahte dosya türü / büyük dosya | reddedilir, yetim dosya kalmaz |
| API token publish kapsamı yok | yazabilir fakat yayınlayamaz |
| Aynı external_id tekrar | mevcut ana kaydı günceller; yeni içerik üretmez |
| Zengin metinde script | temizlenir; public ve panel önizlemede çalışmaz |

Basit CSS satırlarını veya implementation'ı birebir tekrar eden gereksiz testler yazma. İş kuralları, veri bütünlüğü ve yetki sınırlarını doğrula.

## 14. Performans ve işletim kabulü

- Public sayfalar JavaScript kapalıyken temel içerik ve bağlantıları gösterebilmeli; filtre GET formu ve talep POST'u için temel fallback olmalı.
- Liste sorgularında N+1 yok; çeviri ve kapak ilişkileri eager load; uygun locale/status/published_at indeksleri.
- Sayfalama sunucuda; tüm haberleri tarayıcıya indirip sonradan filtreleme yok.
- Lighthouse mobilde performans 90+ hedef; gerçek içerik, ağ ve servis koşullarına bağlı olduğu belirtilir. Ölçüm yapılmadan skor uydurma.
- Büyük görsel için responsive kaynaklar; hero boyutu makul, CLS yaratmayan aspect ratio.
- Admin ve talepler için full-page CDN cache kapalı. Kamuya açık sayfa cache'i form/session güvenliğiyle uyumlu tasarlanır.
- Düzenli MySQL ve medya yedeği; geri yükleme adımı; migration öncesi yedek ve rollback planı.
- İlk sürümde ödeme, sözleşme imzalama, müşteri portalı, proje teslim sistemi, otomatik teklif fiyatı ve ekip chat'i yok. Bunlar ayrı faz olarak değerlendirilebilir.

## 15. Nihai teslimin tanımı

Sadece “tasarım hazır” veya “CRUD oluşturuldu” yeterli değil. Temiz kurulum yapılabilen repo; gerçek admin girişi; yönetilebilen blog ve portföy; tek düğmeli dil filtresi; sürümlü form editörü; kalıcı teklif kaydı; rol kontrolü; çalışan API testleri ve yayın belgeleri birlikte teslim edilmelidir.

Demo içerik ve test hesabı açıkça ayrılmalı. İlk tasarımın görünümü, dil davranışları ve veri akışları ekran görüntüsü + anlamlı feature testleriyle doğrulanmalı. Belgedeki tamamlanmamış görevler işaretlenmeden bırakılmalı.

## 16. Resmî başvuru kaynakları

Bu bağlantılar kurulum sırasında güncel paket uyumluluğu için tekrar kontrol edilecek; belge sürüm numaralarını tahmin ederek sabitlemez.

- Laravel localization: https://laravel.com/docs/localization
- Laravel validation: https://laravel.com/docs/validation
- Laravel deployment: https://laravel.com/docs/deployment
- Laravel queues: https://laravel.com/docs/queues
- Laravel filesystem: https://laravel.com/docs/filesystem
- Laravel Sanctum: https://laravel.com/docs/sanctum
- Filament installation: https://filamentphp.com/docs/5.x/introduction/installation
- Livewire installation: https://livewire.laravel.com/docs/4.x/installation
- Cloudflare Workers dilleri: https://developers.cloudflare.com/workers/languages/

Not: İlk tasarımın gerçek kaynaklarından Laravel/Blade uygulaması geliştirildi. İlk şartname GAZIUSTAM_TASKS.md içinde korunur. Yerel doğrulama ve yayın öncesi açık işler docs/VERIFICATION.md içinde kayıtlıdır.
