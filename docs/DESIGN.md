# Tasarım kaynakları

İlk tasarımın Sites deposu okundu. İncelenen sürüm `02be372f4acc04cc7129095978c158aab4e69c40` (Sites sürüm 6). Kaynakta herkese açık sayfalar ile panel ayrı bileşenlerdi. [İlk tasarım](https://gaziustam.recentcrayon1.chatgpt.site)

| Kaynak | Laravel karşılığı |
| --- | --- |
| `components/Agency.tsx` | Blade ana sayfası ve ortak yerleşim. Hero düzeni, disiplin şeridi, ilk projenin tam genişlikte sunumu, numaralı hizmet listesi, süreç, haberler ve büyük footer yazısı taşındı. |
| `app/globals.css`, `public/agency.css` | `public/studio.css` ilk tasarımın stüdyo seçicilerini, renklerini, tipografisini ve kırılma noktalarını kullanır. |
| `StudioStar` | `components/studio-star.blade.php` özgün SVG geometrisini korur. |
| `ProjectArt` | `components/project-art.blade.php` forma, ora. ve objects. konseptlerini HTML/CSS ile sunar. Gerçek projelerde kapak yüklenebilir. |
| `public/images/chrome-blue-hero.webp` | 1942 × 809, 124.248 bayt. SHA-256 `9896f2ee41fba680753acb4bd9e5b5ff4eab2674a27d179b3921de021d28c1d1`. Ayrıca küçük ekranlar için 640 ve 1100 piksel genişlikte türevler üretildi. |
| `public/images/orange-chair.webp` | 1448 × 1086, 191.754 bayt. SHA-256 `79492cb094ba703acd5b5e2b05044dfc3ab08223d487710be4a445899e1dc8fb`. |
| `lib/i18n.ts`, `lib/translations.txt` | Kaynakta 44 dil tek sözlükte bulunuyor. DE, EN, TR ziyaretçi metinleri adlandırılmış Laravel dil dosyalarına aktarıldı. Taslak formun önizleme mesajları ayıklandı. |

## Dil inceleme listesi

Diğer 41 kaydın çeviri ve yayın incelemesi bekleniyor. Her dil için gezinti, içerik filtreleri, form etiketleri, doğrulama mesajları ve erişilebilirlik metinleri birlikte incelenmeli. Kaynak kodları:

`fr es it nl pt pl cs sk hu ro bg el sv da no fi is et lv lt sl hr sr bs sq mk uk ru be ga mt cy ca eu gl lb hy ka az rm fo`

## Etkileşim

Dil ve mobil gezinti menülerinde yerel HTML `details` kullanılır. Klavye ile açılıp kapanır, JavaScript açıkken Escape odağı menü denetimine döndürür. Hizmet satırı sabit bir hizmet kodunu sorgu parametresine ekleyerek teklif bölümüne gider. Portföy kategori bağlantıları GET ile çalışır. Hero görselinde `srcset`, proje görsellerinde geç yükleme vardır. Hareketi azaltma tercihi geçişleri kapatır.

## Tarayıcı doğrulaması

6 Kasım 2026 tarihinde yerel Laravel sunucusu ve seed edilmiş SQLite veritabanıyla Chromium üzerinde denendi.

| Kontrol | Sonuç |
| --- | --- |
| DE, EN, TR ana sayfaları × 360, 390, 768, 1280, 1440 px | 15/15 HTTP 200. Her boyutta belge genişliği görünüm alanıyla eşit. Dil niteliği doğru, hero yüklendi, teklif formunun POST ve CSRF alanları mevcut. |
| Klavye ve hareket | 390 px üzerinde ilk Tab ile içerik bağlantısı odaklandı. Üç dilde mobil menü açıldı ve Escape ile kapandı. Hareketi azaltma tercihinde kaydırma animasyonu kapandı. |
| Hizmet seçimi | Her dilde ilk hizmet satırı 1440 px üzerinde teklif formunda `ai` seçeneğini açtı. |
| JavaScript kapalı | Almanca ana sayfa, portföy, haber listesi, tüm diller görünümü ve iki hukuki sayfada 390 ve 1440 px kontrolleri geçti. İki genişlikte haber/proje detay bağlantıları çalıştı. Hizmet seçimi formda korundu. |

| Bölüm | Mobil | Masaüstü |
| --- | --- | --- |
| Ana sayfa | [390 px](qa/home-390.png) | [1440 px](qa/home-1440.png) |
| Portföy seçkisi | [390 px](qa/work-390.png) | [1440 px](qa/work-1440.png) |
| Teklif formu | [390 px](qa/form-390.png) | [1440 px](qa/form-1440.png) |

Bu kontroller yerel sayfalar içindir. Üretim altyapısında Lighthouse puanı ve gerçek cihaz performans ölçümü ayrıca yapılmalıdır.

## Panel doğrulaması

7 Kasım 2026 tarihinde form editörü ve zengin içerik editörü 390, 768 ve 1440 px üzerinde açıldı. Yatay taşma görülmedi. Form etiketi değişikliği canlı önizlemede gösterildi. Geçici yerel Owner hesabıyla panel girişi, içerik listesi ve Ayarlar ekranı doğrulandı. Test hesabı işlem sonunda kaldırıldı.

- [Form editörü, masaüstü](qa/admin-form-1440.png)
- [Form editörü, mobil](qa/admin-form-390.png)
- [Zengin editör, masaüstü](qa/admin-editor-1440.png)
- [Zengin editör, mobil](qa/admin-editor-390.png)
