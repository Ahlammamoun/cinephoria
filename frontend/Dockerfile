# Utiliser l'image de Node.js
FROM node:16

# Répertoire de travail dans le conteneur
WORKDIR /usr/src/app

# Copier les fichiers package.json et package-lock.json
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste du code source
COPY . .

# Construire le projet React
RUN npm run build

# Exposer le port pour l'application frontend
EXPOSE 3000

# Lancer le serveur de développement
CMD ["npm", "start"]
