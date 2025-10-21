# CRM Rendszer Követelmények

## Általános követelmények

### 1. Dokumentáltság
- Üzemeltetői (telepítési) kézikönyvek magyar vagy angol nyelven
- Felhasználói kézikönyv vagy e-learning dokumentáció
- Dokumentációnak az aktuális verziószámú modulhoz kell kapcsolódnia

### 2. Fenntarthatóság, terméktámogatás
- Legalább egy éves magyar vagy angol nyelven elérhető támogatási szerződés
- Terméktámogatás biztosítása legalább a fenntartási időszak végéig
- Verziókezelési és karbantartási stratégia dokumentációja

### 3. Törvényi megfelelőség
- Gyártói/szállítói nyilatkozat a hazai törvényi előírásoknak való megfelelésről
- GDPR (2016/679 sz. EU rendelet) megfelelés személyes adatok kezelése esetén
- Adatkezelési szabályzat/tájékoztató megléte

### 4. Integráltság, digitalizáltság
- Integráció a cégnél már meglévő vagy egyidőben bevezetett modulokkal
- Egységes adattárolás, duplikált adatok elkerülése

### 5. Naplózás
- Események, adatváltoztatások naplózása alapvető üzleti folyamatokra
- Online naplómegtekintés az aktív üzleti évben
- Archív naplók a fenntartási időszakban

### 6. Képzés
- Legalább egy alkalmazott képzése felhasználói és adminisztrátori szinten
- Jelenléti képzés vagy távoktatás
- Oktatási és vizsgajegyzőkönyvek vagy e-learning regisztrációk

### 7. Jogosultság-kezelés
- Felhasználók azonosítása
- Különböző funkciók elérhetőségének jogosultsághoz rendelése
- Legalább felhasználó és adminisztrátor elkülönítése

### 8. Referencia
- Legalább 3 darab működő referencia az adott funkcionális célterületen

### 9. Magyar vagy angol nyelvű szoftververzió
- Funkcionalitás legalább magyar vagy angol nyelven elérhető

### 10. Távoli elérés
- Távoli hozzáférés biztosítása a szükséges funkcionalitások tekintetében

### 11. Hibabejelentés dokumentáltsága
- Online hibabejelentő felület a rendszerben

### 12. Bevezetett rendszerek adattöltöttsége és valós használata
- Legalább heti gyakorisággal történő tényleges használat
- Valós üzleti és egyéb adatokkal való töltöttség

### 13. Adatmentés és adatvédelem, üzletfolytonossági követelmények
- Biztonsági mentési funkció
- IT biztonsági protokollok, védelmi eljárások (tűzfal, VPN, stb.)
- IT biztonsági szabályzat, katasztrófavédelmi terv
- Incidensek bejelentési folyamata

## Funkcionális követelmények

### Értékesítési folyamatok optimalizálása

#### 1. Kampánymenedzsment alapok
- Direkt marketing kampányok előkészítése és menedzselése
- Célközönség leválogatása adott paraméterek szerint
- Eredmény exportálása további felhasználásokhoz
- Kapott visszajelzések kezelése (megkeresés eredménye)

#### 2. Értékesítési folyamatok támogatása
- Teljes értékesítési folyamat támogatása: Ajánlat → Rendelés → Szállítás → Számlázás
- Integráció logisztikai és pénzügyi modulokkal
- Értékesítési bizonylatok adatainak egyszeri bevitele
- Gyártásból érkező előrejelzések kezelése (ha releváns)
- Potenciális értékesítési lehetőségek kezelése és nyomon követése
- Ajánlatkészítés támogatása
- Ajánlati árazás, többszintű kedvezményrendszer kezelése

### Ügyfélelégedettség növelése

#### 3. Kedvezmények kezelése
Legalább két kedvezményfajta kezelése az alábbiak közül:
- Mennyiségi kedvezmények
- Egyedi árak
- Értékhatárhoz kötődő kedvezmények
- Időszaki kedvezmények

#### 4. Ügyfélkezelés támogatása
- Feladat kiosztási/hozzárendelési lehetőség
- Ügyféllel történt kapcsolatfelvételek folyamatszemléletű dokumentálása
- Ticketing rendszer
- Utánkövetés és visszakereshetőség
- Elemzési, riportolási lehetőség különböző szempontok mentén
- Emlékeztetők/figyelmeztetések beállítása
- Naptárintegrációs képesség

#### 5. Reklamáció kezelés
- Ügyfél által jelzett problémák rögzítése
- Problémák elemzése
- Szükség esetén eszkaláció

#### 6. Front office támogatás
Legalább két üzenetküldési/kommunikációs mód az alábbiak közül:
- Hangátvitel
- Chat
- Chatbot
- Email
- IVR (Interactive Voice Response)
- SMS
- Közösségi média (Facebook, Twitter, stb.)

### Alapfunkciók

#### 7. Ügyfél törzsadatok kezelése
Az ügyfél törzsadatok tartalmazása:
- Egyedi azonosító
- Ügyfél neve
- Kapcsolattartók
- Címek (számlázási, szállítási)
- Egyedi ügyféljellemzők rögzítésének lehetősége
- Automatikus betöltés külső adatbázisból egyedi kulcsok alapján

#### 8. Adatvédelem
- GDPR és egyéb adatvédelmi előírásoknak való megfelelés
- SSL titkosítás
- Adatmentési, visszaállítási lehetőségek

#### 9. Biztonságos levelezési lehetőség
- Elektronikus levelek olvasása és írása biztonságos távoli kapcsolat nélkül is
- HTTPS kapcsolat, certifikációk használata
- Kettős (addicionális) autentikáció

#### 10. Analitika és jelentéskészítés
- Testreszabható jelentések készítése
- Értékesítési teljesítmény jelentések
- Ügyfélaktivitási jelentések
- Kampányeredmény jelentések
- Vizuális megjelenítés (grafikonok, diagramok)
- KPI-ok és fontos mutatók nyomonkövetése

#### 11. Integráció és testreszabhatóság
- Integráció más üzleti rendszerekkel (ERP, e-mail, naptár)
- API biztosítása egyedi fejlesztések és integrációk számára
