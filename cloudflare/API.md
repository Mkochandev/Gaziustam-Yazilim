# İçerik API'si v1

Owner paneli → API erişimleri: ad ve yetkilerle token oluşturun. Anahtar yalnız o anda gösterilir. `content:write` taslak yazabilir; published sürüm kaydetmek için ayrıca `content:publish` gerekir. `content:read` ana içerik listesini okuyabilir. İptal edilen token anında geçersizdir. Anahtar loglanmamalı, GitHub'a yazılmamalı.

## Oluştur / güncelle

`POST /api/v1/posts`

Header: `Authorization: Bearer <TOKEN>`, `Content-Type: application/json`.

```json
{
  "type": "news",
  "category": "ai",
  "source_locale": "de",
  "provider": "editorial-pipeline",
  "external_id": "news-001",
  "translations": [
    {
      "locale": "de",
      "title": "Eine neue Perspektive",
      "slug": "eine-neue-perspektive",
      "excerpt": "Kurze Beschreibung.",
      "body": "<p>Eigener redaktioneller Inhalt.</p>",
      "status": "draft"
    }
  ]
}
```

Aynı provider + external_id tekrar geldiğinde aynı ana kayıt güncellenir. Sonraki istekte aynı kaynağa `locale: tr` çevirisi eklenebilir; gönderilmeyen dil sürümleri silinmez. İçerik `id` ile de güncellenebilir. Tek istekte en fazla 10 dil sürümü. HTML, slug, tarih, kategori ve dosya referansları admin ile ortak serviste doğrulanır.

Yayınlama: aynı endpoint'e çeviri `status: published` ve isteğe bağlı ISO `published_at` ile gönderilir. Bunun için publish yetkisi gerekir. Body formatı değişmeden diğer dildeki sürüm ayrı yayınlanabilir. Yanıt `{ "id": "..." }`.

`GET /api/v1/posts`: read kapsamı ile son 100 ana içerik kaydı. Bu endpoint editoryal erişim içindir, public içerik listesi değildir. Public HTML listelerinde yayın/dil görünürlüğü ayrıca uygulanır.

Hatalar: 401 token, 403 yetki, 409 slug çakışması, 422 validasyon, 429 limit. Dakikada token başına 60 istek.

Bu sürüm rastgele uzak URL'den dosya indirmez. Görseller panelden yüklenir; `cover_id` mevcut public medya kimliğidir. İçerik üretimi/çeviri sağlayıcısı bu repoda bağlı değildir. Varsayılan ve örnek akış taslaktır; yayın yetkili bir işlem olarak ayrıca verilmelidir.
