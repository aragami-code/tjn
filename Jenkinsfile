pipeline {
    agent any
    
    // Variables d'environnement pour les services Docker Compose
    environment {
        // Le nom de l'utilisateur root MySQL est 'root'
        DB_ROOT_PASSWORD = 'root_password'
        
        // Nom de la base de données pour l'application
        DB_DATABASE = 'web_database' 
        
        // Nom du service MySQL tel que défini dans docker-compose.yml
        DB_HOST = 'mysql' 
        
        // Optionnel : Laissez commenté si le dépôt est public
        // GITHUB_CREDENTIALS = 'votre-credential-id' 
    }

    stages {
        stage('Nettoyage et Préparation') {
            steps {
                echo "Nettoyage des fichiers précédents et préparation du workspace."
                
                // Supprime le répertoire website_files synchronisé avant le clonage
                sh 'sudo rm -rf /vagrant/deploiement/website_files'
                sh 'mkdir /vagrant/deploiement/website_files'
            }
        }
        
        stage('Clonage du Code') {
            steps {
                echo "Clonage du code source depuis GitHub."
                
                // Le code est cloné dans le répertoire workspace de Jenkins
                git branch: 'main', url: 'https://github.com/aragami-code/tjn.git'
            }
        }
        
        stage('Test (Simulation)') {
            steps {
                echo "Exécution des tests unitaires ou d'un simple contrôle de syntaxe..."
                // Vous pouvez ajouter ici des commandes de test PHP (PHPUnit, linting, etc.)
                sh 'php -l index.php' // Vérifie la syntaxe de index.php si PHP est installé dans l'agent Jenkins
            }
        }
        
        stage('Déploiement du Code') {
            steps {
                echo "Copie du code vers le volume partagé (website_files)."
                
                // Copie le contenu du répertoire de travail de Jenkins vers le volume synchronisé de Docker Compose
                // NOTE : Utiliser **/** pour s'assurer que même les fichiers cachés sont copiés.
                sh 'sudo cp -R * /vagrant/deploiement/website_files/'
            }
        }
        
        stage('Redémarrage des Services Docker') {
            steps {
                echo "Redémarrage de l'architecture Docker pour rafraîchir le code."
                
                // Relance seulement les services (nginx et php-fpm) qui utilisent le volume
                sh 'sudo docker compose -f /vagrant/deploiement/docker-compose.yml restart php-fpm nginx_web'
                
                // Affiche l'état des services pour confirmation
                sh 'sudo docker compose -f /vagrant/deploiement/docker-compose.yml ps'
            }
        }
        
        stage('Vérification du Déploiement') {
            steps {
                echo "Le déploiement est terminé. Le site devrait être disponible à http://192.168.56.10:8080"
            }
        }
    }
}
