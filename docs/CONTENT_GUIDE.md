# Ekip ve içerik rehberi

## Hesaplar

Owner tüm modüllere erişir. Editor haber, portföy, kategori, medya ve çeviri yönetir. Sales müşteri taleplerini, notları, durumları ve ekleri yönetir. Owner API ve kullanıcılar ekranından hesap açar, rolü veya etkinliği değiştirir. Son etkin Owner kapatılamaz. Hesap devre dışı bırakıldığında oturumları ve tokenları iptal edilir.

## Haber ve portföy

Panelde Haber ve portföy → Oluştur seçilir. Tür, kaynak dil, kategori, kapak, gerekiyorsa galeri ve sırası belirlenir. Projede konsept, öne çıkarma, sıra, teknolojiler ve canlı URL ayarlanabilir. Müşteri adı isteğe bağlıdır.

Kaydın Dil sürümleri bağlantısında DE/EN/TR sekmeleri vardır. Başlık, slug, özet, zengin gövde, SEO ve kapak alt metni her dilde ayrı girilir. Yeni dil sürümü taslaktır. Kopyalanan metin kendiliğinden çeviri sayılmaz.

Zengin editör başlık, liste, bağlantı ve görsel destekler. Sunucu tüm gövdeyi sanitize eder. Galeri alt metinleri için dosya yolunu anahtar, o dildeki açıklamayı değer girin. Gerekirse HTML düzenleme alanı da kullanılabilir.

Taslak kaydetme ile yayınlama durumunu ayırt edin. Yayında seçilen ve gelecek tarih verilen sürüm zamanı gelene kadar public görünmez. Önizleme kaydedilmiş sürümü gösterir, oturum ve süreli imza gerektirir. Slug değişince eski URL 301 yönlenir. Önceki slug başka bir kayda verilemez.

Public blogda mevcut dil filtresi varsayılan açıktır. Kapatılınca aynı ana içerik tek kart olur. Aktif dil, kaynak dil ve dil önceliği sırasıyla seçilir. Kartın dili ve gerçek URL'si korunur.

## Medya ve kategoriler

Medya ekranından JPEG/PNG/WebP yüklenir. Kapak ve galeri yüklemeleri de kataloglanır. Kullanılan dosya silinemez. Önce içerikteki kapak/galeri/gövde kullanımını kaldırın. API için medya ID'si bu ekrandadır.

Kategori kodu kalıcıdır. Kategori adları dil sürümlerinden düzenlenir. Hizmetten forma geçiş, altı sabit hizmet koduyla çalışır.

## Teklif formu

Owner, Teklif formu ekranında soldan alan seçer, ortada alan ayarlarını değiştirir, sağda anlık önizlemeyi görür. Alanlar sürüklenerek veya yukarı/aşağı düğmeleriyle sıralanır. Dar ekranda bölümler alt alta gelir.

Anahtarlar sabittir. Daha önce yayınlanmış alanın türü değiştirilemez. Yeni bir anlam/tür için yeni anahtar kullanın. contact_name, contact_email ve message etkin ve zorunlu kalır. Alan etiketleri, yer tutucu, yardım, seçenek ve karakter sınırı düzenlenebilir. Çalıştırılabilir validasyon veya kod girilemez.

Kaydet yeni taslak sürüm oluşturur. Önce dil önizlemelerini inceleyin, sonra yayınla düğmesine basın. Yayınlama aktif sürümü atomik değiştirir. Eski açık form 24 saat daha kabul edilir. Önceki talepler kendi etiket ve seçenek snapshot'larını gösterir.

Dosya alanı en fazla 3 ek, her biri 5 MB kabul eder. PDF/JPEG/PNG/WebP izinlidir. Dosya tekrar gönderiminde kullanıcı tarayıcı güvenliği nedeniyle eki yeniden seçmek zorundadır.

## Talepler

Yeni talepler en üstte görünür. İsim/e-posta/referans araması ve durum, hizmet, dil, sorumlu, tarih filtreleri kullanılabilir. Detayda gönderim dilindeki tüm cevaplar görünür. İç notlar yalnız ekip içindir.

Durum veya sorumlu değişikliği geçmişe kaydedilir. Ek indirme yalnız Owner/Sales içindir. Mail başarısız olsa bile kayıt panelde kalır. Ayarlar ekranında bildirimler tekrar denenebilir.

## Diller ve yayın metinleri

DE/EN/TR aktiftir. Diğer diller etkinleştirmeden önce arayüz, doğrulama ve aktif form çevirisini tamamlamayı gerektirir. Eksik çeviri panelde hata verir. Kaynak 44 dilin kalanları tasarım raporunda inceleme listesindedir.

Gizlilik, işletme bilgileri, saklama süresi ve varsa pazarlama izni metinleri yayın öncesi işletme tarafından tamamlanmalıdır. Hazır sayfalarda inceleme beklediği açıkça belirtilir.

