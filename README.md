# Projektitori

**This project, _Projektitori_, was developed as part of my studies in PHP and MySQL as part of the Omnia Full Stack program (2024-2025).**  
It showcases my ability to create functional web applications with user authentication, search functionality, and database integration.

---

**Projektitori** on PHP- ja MySQL-pohjainen verkkosovellus, joka kehitettiin osana Omnian Full Stack -opintojani (2024-2025). Sovellus tarjoaa käyttäjille mahdollisuuden etsiä ja hakea projekteja eri kategorioista, ja se sisältää käyttäjätunnistuksen sekä hakutoiminnallisuuden.

---

## Asennusohjeet

1. **Kloonaa repositorio**:
   ```bash
   git clone https://github.com/irinal2025/projektitori.git
2. **Määritä tietokanta:** Luo MySQL-tietokanta XAMPP:in phpMyAdminissa ja vie tarvittavat taulut.
3. **Käynnistä sovellus**:  
   - Varmista, että XAMPP on käynnissä ja palvelimet (Apache, MySQL) on otettu käyttöön.  
   - Avaa selaimessa sovellus osoitteessa `http://localhost/projektitori` (tai mihin tahansa kansioon XAMPPissa olet tallentanut projektin)..

## Käytetyt teknologiat

- **PHP**
- **MySQL**
- **HTML/CSS**
- **JavaScript**

## Kuinka käyttää

1. Rekisteröidy ja kirjaudu sisään.  
   Valitse rooli rekisteröityessäsi:  
   - **Opiskelija**: Hae projekteja ja hae niitä, jotka vastaavat taitojasi ja kiinnostuksen kohteitasi.  
   - **Projektin tarjoaja**: Voit lisätä ja hallita projekteja, joihin opiskelijat voivat hakea.

2. Etsi projekteja hakutoiminnolla.

3. Hae projekteja, jotka vastaavat taitojasi ja kiinnostuksen kohteitasi (opiskelija) tai tarjoa projekteja (projektin tarjoaja).

## Projektin tietokanta

1. **Luo tietokanta:** Käytä XAMPP:in phpMyAdminia luodaksesi uuden MySQL-tietokannan, esimerkiksi nimeltä `projektitori`.
2. **Vie taulut:** Kopioi ja liitä seuraavat SQL-komennot phpMyAdminin SQL-osioon taulujen luomiseksi (taulujen rakenne voi muuttua, jos sovellus kasvaa tai sitä kehitetään lisää):

   ```sql
   -- Käyttäjätaulu
   -- Tämä taulu tallentaa käyttäjien tiedot, kuten nimen, sähköpostin ja salasanan.
   CREATE TABLE users (
       user_id INT(11) NOT NULL AUTO_INCREMENT,
       user_type ENUM('student', 'provider') NOT NULL, -- Määrittää käyttäjän tyypin (opiskelija tai palveluntarjoaja)
       first_name VARCHAR(50) NOT NULL, -- Etunimi
       last_name VARCHAR(50) NOT NULL, -- Sukunimi
       email VARCHAR(100) NOT NULL, -- Sähköpostiosoite
       password_hash VARCHAR(255) NOT NULL, -- Salasanan hash
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Luomisaika
       email_verified TINYINT(1) DEFAULT 0, -- Sähköpostin vahvistus (0 = ei vahvistettu, 1 = vahvistettu)
       verification_token VARCHAR(255) DEFAULT NULL, -- Vahvistusmerkki
       last_update TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Viimeisin päivitys
       PRIMARY KEY (user_id), -- Asettaa user_id:n ensisijaiseksi avaimeksi
       UNIQUE KEY (email) -- Varmistaa, että sähköposti on uniikki
   );
   
   -- Projektitaulu
   -- Tämä taulu tallentaa projektitiedot, kuten projektin nimen, kuvauksen ja vaaditut taidot.
   CREATE TABLE projects (
       project_id INT(11) AUTO_INCREMENT PRIMARY KEY,
       provider_id INT(11) NOT NULL, -- Palveluntarjoajan ID
       project_name VARCHAR(100) NOT NULL, -- Projektin nimi
       description TEXT NOT NULL, -- Projektin kuvaus
       required_skills VARCHAR(255) DEFAULT NULL, -- Vaaditut taidot
       status ENUM('open', 'in_progress', 'completed') DEFAULT 'open', -- Projektin tila
       skill_level ENUM('Aloittelija', 'Keskitaso', 'Edistynyt') NOT NULL, -- Taidon taso
       deadline DATE DEFAULT NULL, -- Projektin määräaika
       location ENUM('Etätyö', 'Hybridityö', 'Lähityö') NOT NULL, -- Projektin sijainti
       city VARCHAR(100) DEFAULT NULL, -- Kaupunki
       compensation ENUM('kyllä', 'ei') NOT NULL, -- Onko korvausta tarjolla
       project_duration VARCHAR(255) DEFAULT NULL, -- Projektin kesto
       student_benefits TEXT DEFAULT NULL, -- Opiskelijoille tarjottavat edut
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Projektin luomisaika
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Viimeisin päivitys
       provider_name VARCHAR(100) DEFAULT NULL, -- Palveluntarjoajan nimi
       application_deadline DATETIME DEFAULT NULL -- Hakemuksen määräaika
   );
   
   -- Kategoriataulu
   -- Tämä taulu sisältää projektien kategoriat.
   CREATE TABLE categories (
       category_id INT(11) AUTO_INCREMENT PRIMARY KEY,
       category_name VARCHAR(100) NOT NULL -- Kategorian nimi
   );
   
   -- Projektin ja kategorian välinen taulu
   -- Tämä taulu liittää projektit ja kategoriat toisiinsa.
   CREATE TABLE project_categories (
       project_id INT(11) NOT NULL,
       category_id INT(11) NOT NULL,
       PRIMARY KEY (project_id, category_id), -- Yhdistää projektin ja kategorian
       FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE, -- Poistaa projektin poistuessa
       FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE -- Poistaa kategorian poistuessa
   );
   
   -- Hakemustaulu
   -- Tämä taulu tallentaa opiskelijoiden hakemukset projekteihin.
   CREATE TABLE applications (
       application_id INT(11) AUTO_INCREMENT PRIMARY KEY,
       project_id INT(11) NOT NULL, -- Liitetty projekti
       student_id INT(11) NOT NULL, -- Liitetty opiskelija
       motivation TEXT NOT NULL, -- Hakemuskirje projektin tarjoajalle
       status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending', -- Hakemuksen tila
       applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Hakemuksen ajankohta
   );


3. **Mukautukset:** Voit muokata taulujen rakennetta tarpeen mukaan, jos projektisi vaatii erilaisia kenttiä.

---

## Yhteistyö ja tulevaisuuden suunnitelmat

Projektitori on kehityksessä oleva projekti, joka on tällä hetkellä toiminnassa ja sisältää kaikki tärkeimmät ominaisuudet, kuten käyttäjätunnistuksen luonti ja projektien haku. Vaikka sovellus on käyttökelpoinen, se ei ole vielä täysin valmis, ja siinä voi olla vielä parannettavaa ja optimointia.

Tällä hetkellä en suunnittele aktiivista jatkokehitystä, mutta projekti on avoin mahdollisille muutoksille ja parannuksille. Jos sinulla on ideoita tai ehdotuksia, otan mielelläni vastaan palautetta ja keskustelua projektin kehittämisestä.
