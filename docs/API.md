# İçerik API'si

Owner /admin/erisim ekranında kapsam seçerek token oluşturur. Token yalnız ilk yanıtta gösterilir, veritabanında hash saklanır. Son kullanım ve iptal aynı ekrandadır. İstekler HTTPS üzerinden Authorization: Bearer TOKEN ve Accept: application/json kullanır.

| Yöntem ve adres | Kapsam | Davranış |
| --- | --- | --- |
| GET /api/v1/posts | posts:read | Ekip entegrasyonu için sayfalı kayıtlar ve dil sürümleri |
| POST /api/v1/posts | posts:write | provider + external_id ile idempotent oluşturma/güncelleme |
| PUT /api/v1/posts/{id} | posts:write | Aynı dış kimlikli kaydı güncelleme |
| PUT /api/v1/posts/{id}/translations/{locale} | posts:write | İlişkili taslak çeviri |
| POST /api/v1/posts/{id}/translations/{locale}/publish | posts:publish | Ayrı yayınlama |

Yayındaki sürümü veya ana kaydını güncellemek ayrıca posts:publish gerektirir. Taslaklar public listelere, detaylara ve sitemap'e girmez. Yetkili entegrasyonun okuma endpoint'i taslakları içerir.

Ana kayıt örneği

    {
      "type": "news",
      "source_locale": "de",
      "category": "software",
      "provider": "editorial",
      "external_id": "story-001",
      "source_url": "https://example.com/original-source",
      "cover_media_id": null
    }

Çeviri örneği

    {
      "title": "Ein klarer Projektstart",
      "slug": "ein-klarer-projektstart",
      "excerpt": "Ein kurzer Überblick.",
      "body": "<p>Eigener oder lizenzierter Inhalt.</p>",
      "meta_title": "Ein klarer Projektstart",
      "meta_description": "Ein kurzer Überblick."
    }

Gövde sunucuda sanitize edilir. Uzak görseller sunucu tarafından indirilmez. cover_media_id paneldeki medya kimliğidir. Yayınlamada published_at ISO 8601 gönderilebilir. Tarih verilmezse işlem anı kullanılır.

Oluşturma 201, güncelleme 200, alan hatası 422, auth 401, kapsam/rol 403, kimlik çakışması 409, hız limiti 429 döner. Kullanımdaki slug 422 alan hatasıdır.

Node 22 örnek istemcisi ek paket gerektirmez. PowerShell örneği

    $env:GAZIUSTAM_API_URL='https://your-domain.example'
    $env:GAZIUSTAM_API_TOKEN='YOUR_TOKEN'
    node examples/content-client.mjs

İstemci yalnız taslak oluşturur. Yayınlama için ayrı POST isteği ve posts:publish kapsamı gerekir. Harici AI veya otomatik içerik üretim sağlayıcısı bağlı değildir. Özgün/lisanslı girdiler kullanın.

