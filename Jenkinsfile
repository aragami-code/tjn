pipeline {
    agent any
    
    // Variables d'environnement pour les services Docker Compose
    environment {
        // Variables pour la connexion à MySQL
        DB_ROOT_PASSWORD = 'root_password'
        DB_DATABASE = 'web_database' 
        DB_HOST = 'mysql' 
        
        // Configuration du chemin vers Docker Compose sur la VM
        DOCKER_COMPOSE_PATH = '/vagrant/deployment/docker-compose.yml'
        
        // Chemin du volume partagé sur la VM (Doit correspondre à la configuration Vagrant/Docker)
        VOLUME_PATH = '/vagrant/deployment/website_files'
        
        // Optionnel : Laissez commenté si le dépôt est public
        // GITHUB_CREDENTIALS = 'votre-credential-id' 
    }

    stages {
        stage('Nettoyage et Préparation') {
            steps {
                echo "Nettoyage des fichiers précédents et préparation du volume partagé."
                
                // Correction : Utilisation de 'rm -rf' et 'mkdir -p' en une seule commande pour la robustesse et l'absence de 'sudo'
                sh "rm -rf ${VOLUME_PATH} && mkdir -p ${VOLUME_PATH}"
            }
        }
        
        stage('Clonage du Code') {
            steps {
                echo "Clonage du code source depuis GitHub."
                // Le code est cloné dans le workspace de Jenkins
                git branch: 'main', url: 'https://github.com/aragami-code/tjn.git'
            }
        }
        
        stage('Test (Simulation)') {
            steps {
                echo "Exécution des tests de base."
                // sh 'php -l index.php' // Décommenter si l'agent Jenkins a PHP
            }
        }
        
        stage('Déploiement du Code') {
            steps {
                echo "Copie du code du workspace vers le volume partagé."
                
                // Copie le contenu du workspace de Jenkins vers le volume synchronisé
                sh "cp -R * ${VOLUME_PATH}/"
            }
        }
        
        stage('Redémarrage des Services Docker') {
            steps {
                echo "Redémarrage des services PHP et NGINX pour charger le nouveau code."
                
                // Redémarre les services NGINX et PHP-FPM uniquement sans 'sudo'
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
    } // Fin de 'stages'
} // Fin de 'pipeline'
