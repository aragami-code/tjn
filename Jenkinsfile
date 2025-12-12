// Jenkinsfile (Pipeline Scripted)

pipeline {
    agent any

    environment {
        // Chemin cible dans le volume partagé (qui est le même pour NGINX)
        // Ce chemin correspond à ./website_files sur la VM, monté en /var/website_output
        NGINX_DEPLOY_PATH = '/var/website_output'
        // Le nom de l'identifiant de credential SSH ou Username/Password dans Jenkins
        # GITHUB_CREDENTIALS = 'votre-credential-id' // Décommenter si le dépôt est privé
        GIT_REPO_URL = 'https://github.com/[votre-utilisateur]/[votre-depot].git'
    }

    stages {
        stage('Nettoyage Ancien Code') {
            steps {
                echo 'Nettoyage du répertoire de déploiement NGINX...'
                // Exécute la commande de nettoyage directement sur le volume cible
                // Note : Nous utilisons 'sh' car Jenkins s'exécute sur l'agent (le conteneur)
                sh "sudo rm -rf ${NGINX_DEPLOY_PATH}/*"
            }
        }
        
        stage('Clonage Code GitHub') {
            steps {
                echo "Clonage du code depuis ${GIT_REPO_URL}"
                // Récupère la dernière version du code du dépôt
                git url: env.GIT_REPO_URL, branch: 'main'
                
                // Si votre dépôt est privé, utilisez :
                // git url: env.GIT_REPO_URL, branch: 'main', credentialsId: env.GITHUB_CREDENTIALS
            }
        }

        stage('Copie vers NGINX') {
            steps {
                echo 'Copie des fichiers (index.php, etc.) vers le dossier NGINX partagé...'
                // Le dossier courant (workspace) contient le code cloné.
                // Nous copions tout vers le volume monté.
                // Le "sudo" est souvent nécessaire pour écrire sur un volume bind mount depuis Jenkins (qui tourne en user 'jenkins' ou 'root' selon la configuration).
                sh "sudo cp -R ./* ${NGINX_DEPLOY_PATH}/"
                echo "Déploiement terminé. Site mis à jour sur http://192.168.56.10:8080"
            }
        }
    }
    
    post {
        success {
            echo 'Pipeline terminé avec succès.'
        }
        failure {
            echo 'Le déploiement a échoué. Vérifiez les logs de clonage et de copie.'
        }
    }
}
