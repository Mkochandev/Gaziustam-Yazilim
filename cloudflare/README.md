# Cloudflare uygulaması

Ana kurulum rehberi: [proje README](../README.md). Bu klasör bağımsız deploy edilir; PHP veya Node sunucusu üretimde sürekli çalışmaz. Node yalnız Wrangler/yerel geliştirme içindir.

## Kaynak düzeni

- `src/index.js`: HTTP yönlendirme, admin/API, private medya ve güvenlik başlıkları.
- `src/security.js`: oturum/CSRF/hash/yetki/sanitize/upload yardımcıları.
- `src/content.js`: ortak içerik/çeviri sorguları ve kayıt servisi.
- `src/forms.js`: form şeması, kontrollü validasyon ve atomik talep kaydı.
- `src/render.js`: sunucuda HTML; ana sayfa tasarımı burada koddan değişir.
- `src/i18n.json`: arayüz çevirileri; `src/art.json`: mevcut ürün konseptlerinin HTML'i.
- `public/studio.css`, `public/images/`: ilk tasarım kaynakları. `public/admin.*`: özel admin arayüzü.
- `migrations/`: gerçek D1 SQL; `scripts/`: ücretsiz kurulum, owner ve yayın.
- `tests/`: SQLite tabanlı D1 adaptörüyle anlamlı feature testleri.

## Adım adım CLI alternatifi

```sh
npm ci
npx wrangler login
npm run setup
npx wrangler deploy
npx wrangler secret put SESSION_SECRET
npm run owner -- --remote
```

Bu alternatifte SESSION_SECRET'i parola yöneticinizde 32+ karakter üretin. `npm run launch` aynı işleri sır değerini göstermeden otomatik yapar.

Turnstile isteğe bağlı: `wrangler.jsonc` → `vars.TURNSTILE_SITE_KEY`; secret için `npx wrangler secret put TURNSTILE_SECRET`. İkisini birlikte girin. Siteverify arızasında talep kabul edilmez; kullanıcı girdisi korunarak hata gösterilir.

Kendi Workers adınızı kullanacaksanız ilk yayından önce `wrangler.jsonc` içindeki `name` değerini değiştirin. D1 adı ayrı kaynak adıdır. Gerçek database_id'yi uydurmayın; `setup` veya Cloudflare'dan alın. Üretimde `.dev.vars` yüklenmez. Oturum sırrı yalnız Workers secret'ta tutulur.

## İş akışları

İçerik: tür/kategori → DE sürümü → dil sürümü ekle → her dil için taslak/yayın → Kaydet. Gövdede izinli HTML sunucuda temizlenir. Gelecek tarihli yayın sorgu anında değerlendirilir; cron gerektirmez. Slug değişiminde eski yol rezerve kalır ve gerçek yeni yayına 301 olur.

Form: düzenle → yeni taslak → seçili taslağı yayınla. Yayınlanmış sürüm satırı değişmez; önceki sürüm retired olur. Eski açık form 24 saat kabul edilir; daha eski sürümde kullanıcının metinleri kaybolmadan hata gösterilir. Eski talep kendi schema_snapshot'ını kullanır.

Talepler: Yeni / İnceleniyor / Görüşülüyor / Teklif gönderildi / Kabul edildi / Kapatıldı / Spam. Notlar yalnız panelde görünür; ekler yalnız sales/owner tarafından indirilebilir. Editor bu kayıtları okuyamaz.

Medya: public görseller `/media/{id}` üzerinden; private eklerin aynı yolunda yetki kontrolü zorunludur. Public asset dizinine hiçbir talep eki, SQL export veya secret konulmaz.

## Kontrolleri yeniden çalıştırma

Node 24:

```sh
npm ci
npm run check
npm test
npx wrangler deploy --dry-run --outdir .wrangler/check
```

GitHub Actions aynı kontrolleri çalıştırır, kendi başına üretime deploy etmez. CI için Cloudflare tokenı gerekmez.

Resmî kaynaklar: [Workers](https://developers.cloudflare.com/workers/), [D1 başlangıç](https://developers.cloudflare.com/d1/get-started/), [D1 kotaları](https://developers.cloudflare.com/d1/platform/pricing/), [statik varlıklar](https://developers.cloudflare.com/workers/static-assets/).
