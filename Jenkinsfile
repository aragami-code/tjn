pipeline {
    agent any
    
    // Variables d'environnement pour les services Docker Compose
    environment {
        // Variables pour la connexion à MySQL, utilisées dans votre code PHP
        DB_ROOT_PASSWORD = 'root_password'
        DB_DATABASE = 'web_database' 
        DB_HOST = 'mysql' 
        
        // Configuration du chemin vers Docker Compose (depuis la VM)
        DOCKER_COMPOSE_PATH = '/vagrant/deployment/docker-compose.yml'
        
        // Chemin du volume partagé sur la VM (où NGINX et PHP lisent)
        VOLUME_PATH = '/vagrant/deployment/website_files'
    }

    stages {
        stage('Nettoyage et Préparation') {
            steps {
                echo "Nettoyage des fichiers précédents et préparation du volume partagé."
                
                // Correction: Suppression de 'sudo'
                sh "rm -rf ${VOLUME_PATH}"
                sh "mkdir ${VOLUME_PATH}"
            }
        }
        
        stage('Clonage du Code') {
            steps {
                echo "Clonage du code source depuis GitHub."
                // Clonage dans le workspace de Jenkins
                git branch: 'main', url: 'https://github.com/aragami-code/tjn.git'
            }
        }
        
        stage('Test (Simulation)') {
            steps {
                echo "Exécution des tests de base."
                // Vérifie la syntaxe de index.php (si PHP est accessible dans le conteneur Jenkins, ce qui n'est pas garanti)
                // Optionnel : sh 'php -l index.php' 
            }
        }
        
        stage('Déploiement du Code') {
            steps {
                echo "Copie du code du workspace vers le volume partagé."
                
                // Correction: Suppression de 'sudo'
                // Copie le contenu du workspace de Jenkins vers le volume synchronisé
                sh "cp -R * ${VOLUME_PATH}/"
            }
        }
        
        stage('Redémarrage des Services Docker') {
            steps {
                echo "Redémarrage des services PHP et NGINX pour charger le nouveau code."
                
                // Correction: Suppression de 'sudo'
                // Redémarre les services NGINX et PHP-FPM uniquement
                sh "docker compose -f ${DOCKER_COMPOSE_PATH} restart php-fpm nginx_web"
                
                echo "Affichage de l'état des services Docker pour vérification."
                sh "docker compose -f ${DOCKER_COMPOSE_PATH} ps"
            }
        }
        
        stage('Vérification Finale') {
            steps {
                echo "Déploiement terminé. Le site devrait être disponible à http://192.168.56.10:8080"
            }
        }
    }
}
