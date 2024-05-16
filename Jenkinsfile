#!groovy
pipeline {
    agent none
    stages {
        stage('Maven Install') {
            agent {
                docker {
                    image 'maven:3.5.0'
                }
            }
            steps {
                sh 'mvn clean install'
            }
        }
        stage('Build') {
            agent any
            steps {
                sh 'docker build -t balboel/helpdesk-app .'
            }
        }
        stage('Deploy') {
            agent any
            steps {
                sh 'docker run -d -p 8080:8080 balboel/helpdesk-app'
            }
        }
    }
}