# Symfony Thumbnail Generator



## Opis
Aplikacja zbudowana na frameworku Symfony służąca do generowania miniatur
obrazów.

## Wymagania
- Docker
- Docker Compose
- Make (opcjonalnie)

## Instalacja
Aplikacja oparta jest o środowisko Docker. Posiada pliki docker-compose oraz
plik Makefile - oba do ułatwienia pracy. Pobierz repozytorium na dysk i uruchom
komendę make build, która zbuduje aplikację. Możliwe jest uruchomienie tylko
wersji produkcyjnej używają make prod lub make dev dla deweloperskiej. Make down
wyłącza kontenery. Po uruchomieniu kontenerów wchodzimy do konteneru symfony
komendą make ssh i pobieram wszystkie zależności composer install. W folderze
/data/db znajdują się dwie struktury bazy danych dla aplikacji oraz testów,
zaimportuj je do kontenera database dowolnym sposobem.

## Konfiguracja
Wszelkie pliki konfiguracyjne w repozytorium powinny wystarczyć.

## Użycie
Udajemy się na localhost i widzimy formularz do przetwarzania obrazów na
miniatury. Wystarczy uzupełnić nazwę, wybrać przeznacznie tj. dysk lokalny lub
Dropbox i załączyć plik.
W przypadku dysku lokalnego, wygenerowana miniatura powinna pobrać się na dysk,
tutaj token nie jest wymagany.
W przypadku Dropbox należy podać wymagany access token app. Aby go zdobyć
udajemy się na <a href="https://www.dropbox.com/developers/apps" target="_blank">
Dropbox APP console</a> tworzymy tam nową aplikację i dodajemy uprawnienie
"files.content.write". Następnie generujemy token w pierwszej zakładce i
wklejamy go do pola token.
Wszystkie miniatury są zapisywane wraz z tokenami do bazy danych, gdzie czekają
na push do Dropboxa. Możemy wykonać ręcznie push za pomocą komendy CLI
"smartiveapp:thumbnails-push-dropbox". Komenda wysyła wszystkie nie wysłane
jeszcze miniatury na Dropbox, jeśli token będzie prawidłowy. Komenda posiada
zabezpieczenie, które usuwa miniatury z błędnym tokenem lub które napotkają inny
błąd dodatkowo informując ile takich encji zostało przetworzonych. Drugim
sposobem jest poczekanie 5 min, gdy cron Linux wykona tą komendę.


## Testowanie
Aplikacja posiada niepełne pokrycie testami ale posiada za to testy jednostkowe,
integracyjne i funkcjonalne oparte na PHPUnit. Wystarczy uruchomić komendę w
kontenerze ./bin/phpunit tests.

## Biblioteki
Poniżej zamieszczam listę użytych bibliotek oraz powód ich użycia:
- imagine/imagine: Prosta w obsłudze bibliotega do manipulowania obrazami,
skracająca czas implementacji, zmiany obrazu.
- twig/twig: Do budowania templates zamiast zwykłych php tpl.
- twig/extra-bundle: Do debugowania twigów.
- symfony/phpunit-bridge oraz phpunit/phpunit: Mostek oraz biblioteka do
testowania aplikacji.
- symfony/maker-bundle: Przydaje sie do szybszego generowania kodu.
- symfony/browser-kit: Bez tego ciężko o test funkcjonalne.
- symfony/security-csrf: Zabezbieczenie przed atakami csrf na formularze.
- spatie/dropbox-api: Użyłem tej biblioteki do łatwego korzystania z API
Dropbox, a wybrałem ją ponieważ była polecanym SDK na oficjalnej stronie.
- doctrine/*: Dzięki nim nie trzeba rozbudowywać repozytorium bo dostarcza
wiele przydatnych funkcji.
- symfony/validator: Mega ułatwia walidowanie formularzy.
