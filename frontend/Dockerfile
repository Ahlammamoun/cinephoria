# --- PHASE 1: build ---
    FROM node:16 as builder
    WORKDIR /usr/src/app
    
    COPY package*.json ./
    RUN npm install
    
    COPY . .
    RUN npm run build
    
    # --- PHASE 2: run ---
    FROM nginx:alpine
    COPY --from=builder /usr/src/app/build /usr/share/nginx/html
    
    EXPOSE 80
    CMD ["nginx", "-g", "daemon off;"]
    