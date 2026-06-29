# -*- coding: utf-8 -*-
import os
import sys
from fpdf import FPDF

# Ensure output directory exists
os.makedirs('docs', exist_ok=True)

class CustomPDF(FPDF):
    def __init__(self, doc_title):
        super().__init__()
        self.doc_title = doc_title

    def header(self):
        # Green border-like header
        self.set_font('helvetica', 'B', 11)
        self.set_text_color(16, 185, 129) # Emerald Green
        self.cell(0, 8, self.doc_title.upper(), border=0, ln=1, align='L')
        self.set_font('helvetica', 'I', 8)
        self.set_text_color(100, 110, 120)
        self.cell(0, 5, "BENINCLEAN - Interface Web de Monitoring & Gestion des Bacs", border='B', ln=1, align='L')
        self.ln(6)

    def footer(self):
        self.set_y(-15)
        self.set_font('helvetica', 'I', 8)
        self.set_text_color(150, 150, 150)
        # Page numbers
        self.cell(0, 10, f"Page {self.page_no()}/{{nb}}", align='R')
        self.set_x(10)
        self.cell(0, 10, "Documentation Technique Officielle - BENINCLEAN", align='L')

    def add_title(self, text):
        self.set_font('helvetica', 'B', 16)
        self.set_text_color(30, 41, 59)
        self.multi_cell(0, 8, text.encode('latin-1', 'replace').decode('latin-1'), align='L')
        self.ln(6)

    def add_section(self, title):
        self.ln(4)
        self.set_font('helvetica', 'B', 12)
        self.set_text_color(16, 185, 129)
        self.cell(0, 8, title.encode('latin-1', 'replace').decode('latin-1'), border=0, ln=1, align='L')
        self.ln(2)

    def add_subsection(self, title):
        self.set_font('helvetica', 'B', 10)
        self.set_text_color(71, 85, 105)
        self.cell(0, 6, title.encode('latin-1', 'replace').decode('latin-1'), border=0, ln=1, align='L')
        self.ln(1)

    def add_body(self, text):
        self.set_font('helvetica', '', 9.5)
        self.set_text_color(51, 65, 85)
        self.multi_cell(0, 5, text.encode('latin-1', 'replace').decode('latin-1'))
        self.ln(3)

    def add_code(self, text):
        self.set_font('courier', '', 8)
        self.set_text_color(30, 41, 59)
        self.set_fill_color(248, 250, 252) # Light Slate Gray
        self.multi_cell(0, 4.5, text.encode('latin-1', 'replace').decode('latin-1'), border=1, fill=True)
        self.ln(3)


# =========================================================================
# DOCUMENT 1 : PROPOSITIONS D'HEBERGEMENT
# =========================================================================
print("Generation du document 1...")
pdf1 = CustomPDF("Guide d'Hebergement")
pdf1.alias_nb_pages()
pdf1.add_page()
pdf1.add_title("Propositions d'Hebergement et deploiement de la plateforme BENINCLEAN")

pdf1.add_section("1. Introduction")
pdf1.add_body(
    "Pour rendre la plateforme web BENINCLEAN fonctionnelle en conditions reelles, elle doit quitter votre "
    "ordinateur local (XAMPP/localhost) et etre hebergee sur un serveur en ligne. Cet hebergement permettra "
    "a la fois aux utilisateurs (administrateurs et superviseurs) de se connecter de n'importe ou, et aux "
    "bacs connectes (ESP32/IoT) d'envoyer leurs donnees en continu via internet."
)

pdf1.add_section("2. Options d'Hebergement Gratuites")
pdf1.add_body(
    "Les solutions gratuites sont parfaites pour les phases de test ou pour presenter le projet devant un jury "
    "sans frais. Voici les trois meilleures options compatibles avec Laravel :"
)

pdf1.add_subsection("A. Alwaysdata (Recommande)")
pdf1.add_body(
    "- Offre : 100 Mo d'espace disque et base de donnees gratuits a vie.\n"
    "- Avantages : Supporte nativement PHP (jusqu'a 8.3) et les bases de donnees MySQL de XAMPP. L'interface est en francais, "
    "et l'installation de Laravel y est tres bien documentee.\n"
    "- Inconvenient : Espace de stockage limite a 100 Mo (ce qui est suffisant pour le code et une centaine de bacs en test)."
)

pdf1.add_subsection("B. Railway.app")
pdf1.add_body(
    "- Offre : 5$ de credits gratuits ou forfait limite par heure d'utilisation par mois.\n"
    "- Avantages : Deploiement automatique ultra-simple depuis un repertoire GitHub. Railway configure la base de donnees "
    "MySQL et l'environnement PHP en un clic.\n"
    "- Inconvenient : Le serveur s'eteint ou s'arrete si vous depassez le quota mensuel gratuit."
)

pdf1.add_subsection("C. Render.com")
pdf1.add_body(
    "- Offre : Service web gratuit pour herberger du code (avec support de PHP via Docker).\n"
    "- Inconvenient majeur : La base de donnees MySQL n'est pas disponible gratuitement sur Render. Il faut utiliser une base "
    "MySQL externe gratuite chez Alwaysdata ou Aiven.io, et renseigner les acces dans le fichier .env de Render."
)

pdf1.add_section("3. Options d'Hebergement Payantes (Production)")
pdf1.add_body(
    "Pour un deploiement professionnel, permanent et performant, les solutions payantes sont necessaires. "
    "Leur prix reste tres abordable :"
)

pdf1.add_subsection("A. Hebergement Mutualise (Le plus simple - 3 a 6 euros / mois)")
pdf1.add_body(
    "Des prestataires comme Hostinger, o2switch ou LWS proposent des hebergements mutualises optimises pour Laravel.\n"
    "- Avantages : Tres bon marche, nom de domaine gratuit souvent inclus, base de donnees MySQL incluse, interface de gestion simplifiee (cPanel).\n"
    "- Comment faire : Il suffit d'envoyer le code via FTP (FileZilla), importer la base de donnees et faire pointer le domaine vers le dossier /public."
)

pdf1.add_subsection("B. VPS - Serveur Dedie Virtuel (Pour le controle total - 4 a 8 euros / mois)")
pdf1.add_body(
    "Des services comme OVHcloud (VPS Starter), Scaleway ou Hetzner louent de petits serveurs virtuels dedies sous Linux.\n"
    "- Avantages : Controle absolu (acces SSH root), meilleures performances.\n"
    "- Inconvenients : Demande des competences administratives pour installer PHP, Apache/Nginx, Composer et MySQL a la main."
)

pdf1.add_section("4. Procedure de deploiement pas-a-pas sur serveur en ligne")
pdf1.add_body(
    "1. Exporter la base de donnees locale depuis phpMyAdmin (XAMPP) en un fichier 'database.sql'.\n"
    "2. Creer une base de donnees MySQL chez votre hebergeur et y importer le fichier 'database.sql'.\n"
    "3. Creer un compte d'hebergement et envoyer l'ensemble des fichiers du projet sur le serveur (via FTP ou via GitHub).\n"
    "4. Creer et configurer le fichier .env en production. Remplacer les identifiants locaux par ceux fournis par l'hebergeur :\n"
    "   APP_ENV=production\n"
    "   APP_DEBUG=false\n"
    "   DB_HOST=adresse_du_serveur_mysql\n"
    "   DB_DATABASE=nom_de_la_base_du_serveur\n"
    "   DB_USERNAME=utilisateur_fourni\n"
    "   DB_PASSWORD=mot_de_passe_fourni\n"
    "5. Via la console SSH du serveur, lancer : 'composer install --no-dev --optimize-autoloader'.\n"
    "6. Configurer la redirection du nom de domaine pour pointer directement vers le sous-dossier '/public' du projet pour securiser le reste du code."
)

pdf1.output("docs/1_Propositions_Hebbergement.pdf")


# =========================================================================
# DOCUMENT 2 : INTEGRATION IOT PLATFORMIO
# =========================================================================
print("Generation du document 2...")
pdf2 = CustomPDF("Integration IoT PlatformIO")
pdf2.alias_nb_pages()
pdf2.add_page()
pdf2.add_title("Guide d'integration des donnees reelles depuis PlatformIO (ESP32) vers XAMPP")

pdf2.add_section("1. Principe general")
pdf2.add_body(
    "Le systeme IoT est compose d'un microcontroleur ESP32 et d'un capteur a ultrasons (ex : HC-SR04) place sous le "
    "couvercle du bac. Ce capteur mesure la distance restante entre le haut du bac et les dechets. L'ESP32 calcule "
    "le pourcentage de remplissage et l'envoie en HTTP POST sous format JSON a la plateforme web BENINCLEAN. "
    "L'application traite les donnees en temps reel dans la base de donnees XAMPP."
)

pdf2.add_section("2. Route API de Reception Laravel")
pdf2.add_body(
    "J'ai configure l'application Laravel pour recevoir les donnees IoT sur l'URL suivante :\n"
    "POST http://<IP_DU_PC>:8000/api/bins/telemetry\n\n"
    "Cette route est exemptee de la validation de jeton CSRF (via bootstrap/app.php) pour permettre aux objets connectes "
    "d'y acceder directement. Elle valide que le code du bac existe en base de donnees et met a jour son taux de remplissage."
)

pdf2.add_subsection("Format JSON attendu par l'API :")
pdf2.add_code(
    '{\n'
    '  "code": "B105",\n'
    '  "fill_level": 75\n'
    '}'
)

pdf2.add_subsection("Exemple de script de traitement dans BinController.php :")
pdf2.add_code(
    'public function updateTelemetry(Request $request)\n'
    '{\n'
    '    $validated = $request->validate([\n'
    '        "code" => "required|string|exists:bins,code",\n'
    '        "fill_level" => "required|integer|min:0|max:100"\n'
    '    ]);\n'
    '    $bin = Bin::where("code", $validated["code"])->firstOrFail();\n'
    '    $bin->fill_level = $validated["fill_level"];\n'
    '    // recalcul automatique du statut (Normal, Presque Plein, Plein) et gestion des alertes...\n'
    '    $bin->save();\n'
    '}'
)

pdf2.add_section("3. Code source C++ pour l'ESP32 sur PlatformIO")
pdf2.add_body(
    "Voici le code source C++ a implementer sur votre projet PlatformIO. Il utilise la bibliotheque ArduinoJson "
    "pour formater les donnees et HTTPClient pour envoyer la requete HTTP POST :"
)

pdf2.add_code(
    '#include <WiFi.h>\n'
    '#include <HTTPClient.h>\n'
    '#include <ArduinoJson.h>\n\n'
    'const char* ssid = "VOTRE_NOM_WIFI";\n'
    'const char* password = "VOTRE_MOT_DE_PASSE_WIFI";\n'
    'const char* serverUrl = "http://192.168.1.50:8000/api/bins/telemetry"; // Remplacer par l\'IP de votre PC\n\n'
    'const int trigPin = 12; // Broche Trig du capteur HC-SR04\n'
    'const int echoPin = 13; // Broche Echo du capteur HC-SR04\n'
    'const float depthOfBin = 100.0; // Profondeur totale du bac en cm\n\n'
    'void setup() {\n'
    '    Serial.begin(115200);\n'
    '    pinMode(trigPin, OUTPUT);\n'
    '    pinMode(echoPin, INPUT);\n'
    '    WiFi.begin(ssid, password);\n'
    '    while (WiFi.status() != WL_CONNECTED) {\n'
    '        delay(500); Serial.print(".");\n'
    '    }\n'
    '    Serial.println("\\nWiFi connecte !");\n'
    '}\n\n'
    'void loop() {\n'
    '    // Mesure de distance par ultrasons\n'
    '    digitalWrite(trigPin, LOW); delayMicroseconds(2);\n'
    '    digitalWrite(trigPin, HIGH); delayMicroseconds(10);\n'
    '    digitalWrite(trigPin, LOW);\n'
    '    long duration = pulseIn(echoPin, HIGH);\n'
    '    float distance = duration * 0.034 / 2.0;\n'
    '    if (distance > depthOfBin) distance = depthOfBin;\n\n'
    '    // Calcul du pourcentage de remplissage\n'
    '    int fillLevel = (int)(((depthOfBin - distance) / depthOfBin) * 100);\n'
    '    if (fillLevel < 0) fillLevel = 0;\n\n'
    '    if (WiFi.status() == WL_CONNECTED) {\n'
    '        HTTPClient http;\n'
    '        http.begin(serverUrl);\n'
    '        http.addHeader("Content-Type", "application/json");\n\n'
    '        StaticJsonDocument<200> doc;\n'
    '        doc["code"] = "B102"; // Mettre le code correspondant au bac\n'
    '        doc["fill_level"] = fillLevel;\n\n'
    '        String requestBody;\n'
    '        serializeJson(doc, requestBody);\n'
    '        int httpResponseCode = http.POST(requestBody);\n'
    '        if (httpResponseCode > 0) {\n'
    '            Serial.println("Donnees envoyees ! Reponse : " + http.getString());\n'
    '        } else {\n'
    '            Serial.println("Erreur d\'envoi : " + String(httpResponseCode));\n'
    '        }\n'
    '        http.end();\n'
    '    }\n'
    '    delay(15000); // Envoi toutes les 15 secondes pour le test\n'
    '}'
)

pdf2.add_section("4. Configuration Reseau requise (Important)")
pdf2.add_body(
    "1. L'ESP32 et le PC executant XAMPP doivent etre connectes sur le MEME reseau Wi-Fi (ex: votre telephone en partage "
    "de connexion ou la box internet du domicile).\n"
    "2. Ne pas utiliser 'localhost' or '127.0.0.1' dans le code de l'ESP32 car pour l'ESP32, localhost designe lui-meme. "
    "Vous devez utiliser l'adresse IP locale de votre ordinateur (ex: 192.168.1.50).\n"
    "3. Pour trouver l'IP de votre PC, ouvrez le terminal PowerShell et tapez 'ipconfig'. Cherchez 'Adresse IPv4'.\n"
    "4. Lancez le serveur Laravel avec la commande suivante pour qu'il ecoute sur tout le reseau et pas seulement sur localhost :\n"
    "   php artisan serve --host=0.0.0.0 --port=8000"
)

pdf2.output("docs/2_Integration_IoT_PlatformIO.pdf")


# =========================================================================
# DOCUMENT 3 : ARCHITECTURE TECHNIQUE & DEFORMATION COMPLET
# =========================================================================
print("Generation du document 3...")
pdf3 = CustomPDF("Architecture Technique & Formation")
pdf3.alias_nb_pages()
pdf3.add_page()
pdf3.add_title("Guide d'Architecture Technique et de Formation Globale - BENINCLEAN")

pdf3.add_section("1. Structure Globale des Repertoires de Laravel")
pdf3.add_body(
    "Pour comprendre comment est structure ce projet et ou modifier les fichiers si vous voulez dupliquer "
    "le site ou changer des elements, voici une description complete des repertoires de Laravel :\n\n"
    "- app/ : Contient le coeur de l'application en PHP. C'est ici que se trouve toute la logique metier "
    "(les modeles, les controleurs et les regles metier).\n"
    "- bootstrap/ : Configure l'initialisation du framework, des middlewares de securite et de routage.\n"
    "- config/ : Regroupe l'ensemble des fichiers de configuration globale (base de donnees, sessions, mail, etc.).\n"
    "- database/ : Contient les scripts pour creer les tables MySQL (migrations) et generer des fausses donnees (seeders & factories).\n"
    "- public/ : Le seul dossier visible par internet. Contient le point d'entree index.php, les fichiers CSS, JS et les images.\n"
    "- resources/ : Contient les interfaces graphiques (les fichiers de vue Blade HTML, traduits et compiles).\n"
    "- routes/ : Definit toutes les URLs de votre application web et les fonctions associees.\n"
    "- storage/ : Stocke les fichiers generes en ecriture par l'application (les logs de crash, les configurations locales).\n"
    "- .env : Fichier contenant les variables d'environnement. C'est ici qu'on configure le nom du site et les acces a XAMPP."
)

pdf3.add_section("2. Guide Detaille des Fichiers du Projet (Fiche par Fiche)")
pdf3.add_body(
    "Voici l'explication complete de chaque fichier cree ou modifie dans ce projet et sa fonction precise :"
)

pdf3.add_subsection("A. Fichier d'environnement : .env")
pdf3.add_body(
    "Ce fichier sert a configurer l'application sans toucher au code PHP. Par exemple, pour changer le nom global de la plateforme, "
    "il suffit de modifier la variable APP_NAME (ex: APP_NAME=BENINCLEAN). Il sert aussi a specifier a Laravel le port et le mot de "
    "passe pour se connecter a la base de donnees MySQL de XAMPP."
)

pdf3.add_subsection("B. Les Modeles de donnees (app/Models/)")
pdf3.add_body(
    "Les modeles font le pont entre le code PHP et vos tables SQL via l'ORM 'Eloquent'.\n"
    "- User.php : Modolise la table des utilisateurs. Contient l'attribut #[Fillable] qui securise les colonnes pouvant etre inserees "
    "par formulaire (name, email, password, role, is_active).\n"
    "- Bin.php : Represente un bac a ordures avec son code unique, sa position GPS (latitude, longitude), son niveau et son statut.\n"
    "- Alert.php : Lie les alertes actives ou resolues aux bacs (relation d'appartenance avec la fonction bin()).\n"
    "- Collection.php : Modolise une session de ramassage de dechets planifiee ou terminee."
)

pdf3.add_subsection("C. Les Controleurs (app/Http/Controllers/)")
pdf3.add_body(
    "Les controleurs recoivent les requetes des utilisateurs, interrogent la base de donnees et renvoient la bonne vue HTML.\n"
    "- UserController.php : Contient toute la logique de gestion des membres. La methode store() valide les entrees, crypte le "
    "mot de passe avec '\\Hash::make()' et cree l'utilisateur. La methode update() modifie un utilisateur existant (permettant "
    "de changer son mot de passe). La methode toggleStatus() change l'etat actif/desactive d'un compte. La methode destroy() supprime "
    "definitivement un compte en base de donnees. Des controles de securite verifient que seul un Administrateur execute ces actions.\n"
    "- BinController.php : Gere l'affichage paginee des bacs et implemente la methode updateTelemetry() qui recoit les donnees JSON "
    "du capteur IoT de l'ESP32 pour mettre a jour le niveau de remplissage, recalculer son etat et declencher/resoudre des alertes.\n"
    "- SettingsController.php : Gere le formulaire de modification de la ville et des seuils. Il deplace geographiquement "
    "tous les bacs de la base de donnees autour de la nouvelle ville selectionnee et recalcule les statuts en fonction des nouveaux seuils.\n"
    "- ReportController.php : Filtre et prepares les KPIs (niveaux moyens, taux d'alertes) et les series temporelles pour le graphique "
    "de niveaux de remplissage selon la periode selectionnee.\n"
    "- MapController.php : Recupere la liste des bacs et calcule le point central d'affichage en fonction de la ville configuree."
)

pdf3.add_subsection("D. Le Gestionnaire Persistant (app/Helpers/SettingsHelper.php)")
pdf3.add_body(
    "Ce fichier a ete cree pour corriger le probleme de la deconnexion. Laravel utilise les sessions utilisateur pour stocker des "
    "donnees temporaires, mais celles-ci sont detruites lors d'une deconnexion. SettingsHelper.php contourne ce probleme en ecrivant "
    "les parametres (ville d'intervention, seuils de remplissage) dans un fichier physique 'storage/app/settings.json' sur le disque. "
    "Les parametres restent ainsi sauvegardes de maniere permanente et independante des sessions !"
)

pdf3.add_subsection("E. Les Vues Blade (resources/views/)")
pdf3.add_body(
    "Ce sont les squelettes HTML dynamises par des balises Blade (boucles @foreach, conditions @if).\n"
    "- layouts/app.blade.php : Template parent contenant le design general du site. Il integre Lucide Icons pour les icones, "
    "Leaflet pour la carte et Chart.js pour les graphiques.\n"
    "- partials/sidebar.blade.php / navbar.blade.php : Les menus de navigation gauche et haut.\n"
    "- dashboard.blade.php : Affiche les stats globales, la carte avec marqueurs colores (vert/jaune/rouge) et le graphique "
    "en courbes connecte a Chart.js.\n"
    "- users/index.blade.php : Contient le tableau des membres avec boutons d'actions et fenetres surgissantes (modales) codees "
    "en HTML/JS pour creer et modifier les profils et les mots de passe.\n"
    "- reports/index.blade.php : Affiche les rapports d'activite filtrables par dates, le bouton d'impression, et integre la "
    "feuille de style '@media print' pour masquer la navigation lors de l'impression et generer un PDF propre."
)

pdf3.add_section("3. Guide pas-a-pas pour Concevoir un Projet Similaire")
pdf3.add_body(
    "Si vous devez developper une autre application du meme type a l'avenir, suivez ces étapes :\n\n"
    "1. Creer l'ossature : Ouvrir un terminal et lancer la commande 'composer create-project laravel/laravel nom_projet'.\n"
    "2. Installer la securite : Installer Laravel Breeze avec 'composer require laravel/breeze --dev' puis la commande "
    "'php artisan breeze:install' pour avoir les pages de Connexion, Inscription et profil pretes.\n"
    "3. Creer la Base de donnees : Lancer XAMPP, ouvrir phpMyAdmin, creer une base de donnees et renseigner ses acces dans le fichier '.env'.\n"
    "4. Creer les structures (Migrations) : Generer des tables SQL via Laravel en utilisant la commande 'php artisan make:migration'. "
    "Renseigner les types de colonnes (ex: string('code'), integer('fill_level'), decimal('latitude', 10, 8)). Executer les migrations "
    "avec 'php artisan migrate'.\n"
    "5. Programmer la logique (Controleurs) : Creer un controleur avec 'php artisan make:controller NomController'. Y ecrire des "
    "methodes PHP pour aller chercher les donnees en base de donnees (ex: 'Bin::all()') et les envoyer aux vues.\n"
    "6. Structurer les Vues : Creer des fichiers '.blade.php' dans resources/views/. Utiliser Tailwind CSS pour mettre en page "
    "des tableaux, des cartes statistiques et des boutons modernes.\n"
    "7. Cartes et Graphiques : Ajouter les liens CDN de Leaflet.js et Chart.js dans votre layout de base, et utiliser Javascript "
    "pour transformer vos donnees de base de donnees (converties en JSON avec la directive '@json($variable)') en points sur la carte "
    "ou en graphiques dynamiques."
)

pdf3.output("docs/3_Architecture_Technique.pdf")

print("Tous les documents PDF ont ete mis a jour avec succes !")
