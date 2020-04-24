#!/bin/bash

# Exit if any command fails
set -e

echo -e "Setting up Local SSL...\n"

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

# Check for required files.
echo -e "\nChecking required files...\n"
if [ -f ./ssl/localhost.ext ]
then
    echo -e "${GREEN}✓${NC} './ssl/localhost.ext' exists."
else
    echo -e "${RED}✗ Missing required file: './ssl/localhost.ext'.${NC}"
    exit 1
fi

if [ -f ./ssl/ca-opts.conf ]
then
    echo -e "${GREEN}✓${NC} './ssl/ca-opts.conf' exists."
else
    echo -e "${RED}✗ Missing required file: './ssl/ca-opts.conf'.${NC}"
    exit 1
fi

echo -e "\nChecking for local Certificate Authority...\n"
# Create folder if needed.
if [ -d ~/.localssl ]
then
    echo -e "${GREEN}✓${NC} '~/.localssl' exists."
else
    echo -e "${RED}✗${NC} '~/.localssl' not found..."
    echo -e "Creating ~/.localssl ..."
    mkdir -p ~/.localssl
    echo -e "${GREEN}✓${NC} '~/.localssl' created."
fi

# Create localhostCA.key if needed.
if [ -f ~/.localssl/localhostCA.key ]
then
    echo -e "${GREEN}✓${NC} 'localhostCA.key' exists."
else
    echo -e "${RED}✗${NC} 'localhostCA.key' not found..."
    echo -e "Creating 'localhostCA.key' ..."
    openssl genrsa -des3 -out ~/.localssl/localhostCA.key 2048
    echo -e "${GREEN}✓${NC} 'localhostCA.key' created."
fi

# Create localhostCA.pem if needed.
if [ -f ~/.localssl/localhostCA.pem ]
then
    echo -e "${GREEN}✓${NC} 'localhostCA.pem' exists."
else
    echo -e "${RED}✗${NC} 'localhostCA.pem' not found..."
    echo -e "Creating 'localhostCA.pem' ..."
    openssl req -x509 -config ./ssl/ca-opts.conf -new -nodes -key ~/.localssl/localhostCA.key -sha256 -days 825 -out ~/.localssl/localhostCA.pem
    echo -e "${GREEN}✓${NC} 'localhostCA.pem' created."
    echo -e "Attempting to Trust the CA..."
    sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain ~/.localssl/localhostCA.pem
    echo -e "${GREEN}✓${NC} Trusted the CA!"
fi

echo -e "\nChecking for local files...\n"

# Create localhost.key if needed.
if [ -f ./ssl/localhost.key ]
then
    echo -e "${GREEN}✓${NC} 'localhost.key' exists."
else
    echo -e "${RED}✗${NC} 'localhost.key' not found..."
    echo -e "Creating 'localhost.key' ..."
    openssl genrsa -out ./ssl/localhost.key 2048
    echo -e "${GREEN}✓${NC} 'localhost.key' created."
fi

# Create localhost.csr if needed.
if [ -f ./ssl/localhost.csr ]
then
    echo -e "${GREEN}✓${NC} 'localhost.csr' exists."
else
    echo -e "${RED}✗${NC} 'localhost.csr' not found..."
    echo -e "Creating 'localhost.csr' ..."
    openssl req -new -config ./ssl/ca-opts.conf -key ./ssl/localhost.key -out ./ssl/localhost.csr
    echo -e "${GREEN}✓${NC} 'localhost.csr' created."
fi

# Create localhost.crt if needed.
if [ -f ./ssl/localhost.crt ]
then
    echo -e "${GREEN}✓${NC} 'localhost.crt' exists."
else
    echo -e "${RED}✗${NC} 'localhost.crt' not found..."
    echo -e "Creating 'localhost.crt' ..."
    openssl x509 -req -in ./ssl/localhost.csr -CA ~/.localssl/localhostCA.pem -CAkey ~/.localssl/localhostCA.key -CAcreateserial -out ./ssl/localhost.crt -days 825 -sha256 -extfile ./ssl/localhost.ext
    echo -e "${GREEN}✓${NC} 'localhost.crt' created."
fi

echo -e "\nFinished."
