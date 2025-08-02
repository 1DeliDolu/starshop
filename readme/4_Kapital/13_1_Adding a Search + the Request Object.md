# 🔎 Adding a Search + the Request Object

## 1. Search Bar'ın Template'e Eklenmesi

`templates/part/index.html.twig` dosyasının en üstüne bir arama kutusu ekledik. Formun kodu şu şekilde:

```twig
<form method="get" action="{{ path('app_part_index') }}">
    <input type="text"
           placeholder="Search..."
           name="query"
           value="{{ app.request.query.get('query') }}"
           class="w-full p-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
    >
    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A8.5 8.5 0 1011 19.5a8.5 8.5 0 005.65-2.85z" />
    </svg>
</form>
```

Burada dikkat edilmesi gereken, `value` alanında doğrudan `app.request.query.get('query')` kullanılmasıdır. Böylece arama yaptıktan sonra kutuda aradığımız değer kaybolmaz.

---

## 2. Controller'da Search Parametresinin Kullanılması

`src/Controller/PartController.php` dosyasında, arama parametresi alınırken null gelirse hata olmaması için şu şekilde güncellendi:

```php
$query = $request->query->get('query') ?? '';
$parts = $repository->findAllOrderedByPrice($query);
```

Böylece, arama parametresi yoksa boş string gönderilir ve type error alınmaz.

---

## 3. Repository'de Çoklu Alan Arama

`src/Repository/StarshipPartRepository.php` dosyasında, hem parça adı hem de notlar alanında arama yapılabilmesi için şu kod kullanıldı:

```php
if ($search) {
    $qb->andWhere('LOWER(sp.name) LIKE :search OR LOWER(sp.notes) LIKE :search')
        ->setParameter('search', '%' . strtolower($search) . '%');
}
```

Bu sayede, arama kutusuna yazılan kelime hem isimde hem de notlarda aranır. Mantıksal kontrol için `andWhere()` içinde `OR` kullanıldı.

---

Artık arama kutusu hem isim hem de notlar alanında çalışıyor ve arama değeri kutuda korunuyor.
