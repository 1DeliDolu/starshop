# 🤖 Accessing Data on a ManyToMany / ManyToMany Üzerinden Veriye Erişim

Aslında basit bir hedef: bir yıldız gemisine atanan tüm droidleri yazdırmak. Eğer bir `OneToMany` ilişkisinden, yıldız gemisinin parçalarına ulaşmaya alıştıysan, bundan da çok keyif alacaksın!

`templates/starship/show.html.twig` şablonunu aç. `arrived at` için kullanılan `h4` ve `p` etiketlerini al, aşağıya yapıştır ve `h4` başlığını `Droids` olarak değiştir. `arrived at ...` kısmını temizle ve satırı böl:

---


```twig
// templates/starship/show\.html.twig
// ... lines 1 - 4
{% block body %}
// ... lines 6 - 19
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
        // ... lines 21 - 25
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
                // ... lines 29 - 58
                    <h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">
                        Droids
                    </h4>
                    <p class="text-[22px] font-semibold">
                // ... lines 63 - 67
                    </p>
                // ... lines 69 - 84
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

👉 Bu kısım, Starship şablonunda droidler için bir başlık ve alan oluşturur.

---

Bir `ship` değişkenimiz var ve bu bir `Starship` nesnesi. Ve unutma, bunun bir `droids` özelliği ve bir `getDroids()` metodu var. Yani `for droid in ship.droids` diyebilirsin. Bu, `getDroids()` metodunu çağırır ve bir `Droid` nesnesi koleksiyonu döndürür. Böylece `{{ droid.name }}` diyebiliriz.

---

## 🌀 The loop.last / Döngüde Virgül Kullanımı

Virgül koymak istiyorum, ama sonda fazladan virgül olmasın. Şöyle yapabilirsin: `{% if not loop.last %}, {% endif %}`. Daha havalı yolları var, ama şimdilik basit tut.

Hiç droid yoksa, `else` etiketiyle "No droids on board (clean up your own mess)" yaz. Kaba!

---


```twig
// templates/starship/show\.html.twig
// ... lines 1 - 4
{% block body %}
// ... lines 6 - 19
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
// ... lines 21 - 25
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
// ... lines 29 - 58
                    <h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">
                        Droids
                    </h4>
                    <p class="text-[22px] font-semibold">
                        {% for droid in ship.droids %}
                                {{ droid.name }}{% if not loop.last %}, {% endif %}
                            {% else %}
                                No droids on board (clean up your own mess)
                        {% endfor %}
                    </p>
// ... lines 69 - 84
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

👉 Bu kod bloğu, droid isimlerini virgül ile ayırır; hiç droid yoksa özel bir mesaj gösterir.

---

## 🏠 Droids on the Homepage / Ana Sayfada Droidler

Ana sayfada da droidleri göstermek istiyoruz. `templates/main/homepage.html.twig` şablonunu aç. Parçalardan hemen sonra yeni bir div ekle ve şunu yaz: `Droids: {{ ship.droidNames ?: 'none' }}`

---


```twig
// templates/main/homepage.html.twig
// ... lines 1 - 4
{% block body %}
    <main class="flex flex-col lg:flex-row">
// ... lines 7 - 8
        <div class="px-12 pt-10 w-full">
// ... lines 10 - 17
            <div class="space-y-5">
                {% for ship in ships %}
                    <div class="bg-[#16202A] rounded-2xl pl-5 py-5 pr-11 flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between">
                        <div class="flex justify-center min-[1174px]:justify-start">
                            <img class="h-[83px] w-[84px]" src="{{ asset(ship.statusImageFilename) }}" alt="Status: {{ ship.statusString }}">
                            <div class="ml-5">
                            // ... lines 24 - 36
                                <div>
                                    Parts: {{ ship.parts|length }}</div>
                                <div>
                                    Droids: {{ ship.droidNames ?: 'none' }}
                                </div>
                            </div>
                        </div>
                    // ... lines 44 - 54
                    </div>
                {% endfor %}
            </div>
        // ... lines 58 - 73
        </div>
    </main>
{% endblock %}
```

👉 Bu kod, her yıldız gemisi için droid isimlerini ana sayfada gösterir; yoksa "none" yazar.

---

## 🧠 The Smart Method / Akıllı Yöntem

Virgül ekleme işini tekrar döngüyle yapabilirdik, ama droid isimlerine iki yerde ihtiyaç duyduğumuz için, bunun için bir akıllı metod ekleyelim. Bu metodu `Starship` sınıfının altına ekle: `public function getDroidNames(): string`. Droid isimlerini virgülle ayırıp string olarak döndürmek için şunu kullan:

---


```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 223
    public function getDroidNames(): string
    {
        return implode(', ', $this->droids->map(fn(Droid $droid) => $droid->getName())->toArray());
    }
}
```

👉 Bu metod, droid isimlerini virgülle ayrılmış şekilde string olarak döndürür.

---

Kısa açıklama:
Önce `$this->droids`, elimizdeki droid nesnelerinin koleksiyonu. `map()`, koleksiyondaki her bir droid için fonksiyon çalıştırır. `fn(Droid $droid) => $droid->getName()`, her droidin adını alır. `toArray()`, koleksiyonu diziye çevirir. Son olarak `implode(', ', ...)`, o diziyi virgül ile ayrılmış bir string haline getirir.

Artık `getDroidNames()` metodu sayesinde, `{{ ship.droidNames ?: 'none' }}` diyebiliyoruz.

Hepsi bu kadar! Sayfayı yenile... ve ana sayfada droid isimlerinin tadını çıkar.

---

Sonraki: Foundry ile ManyToMany ilişkisinin fixture'larda nasıl ayarlanacağını göreceğiz. Foundry burada da parlıyor!
