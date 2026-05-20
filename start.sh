#!/bin/bash
# Script de primer inicio para FishSpot Aragón

set -e

echo "================================================"
echo "   FishSpot Aragón — Inicio del entorno Docker  "
echo "================================================"
echo ""

# Verificar que existe .env
if [ ! -f .env ]; then
    echo "[1/4] Copiando .env.example a .env..."
    cp .env.example .env
else
    echo "[1/4] Archivo .env ya existe."
fi

# Construir e iniciar contenedores
echo "[2/4] Construyendo e iniciando contenedores Docker..."
docker compose up -d --build

echo "[3/4] Esperando a que los servicios estén listos (30s)..."
sleep 30

echo "[4/4] Verificando estado..."
docker compose ps

echo ""
echo "================================================"
echo "  Entorno listo:"
echo "  Aplicación:  http://localhost:8000"
echo "  phpMyAdmin:  http://localhost:8080"
echo "                                               "
echo "  Usuario demo: demo@fishspot.local / password  "
echo "================================================"
