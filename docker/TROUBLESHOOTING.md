# 🔧 Guia de Troubleshooting - Conexão PostgreSQL Externa

Este guia ajuda a resolver problemas de conectividade com o banco PostgreSQL externo.

## 🚨 Erro Comum: "connection to server failed"

### Sintomas
```
SQLSTATE[08006] [7] connection to server at "177.44.248.74", port 5432 failed: 
FATAL: no pg_hba.conf entry for host "172.22.0.2", user "postgres", database "staging", SSL encryption
```

### Causas Possíveis

1. **Configuração pg_hba.conf do PostgreSQL**
2. **Configuração postgresql.conf do PostgreSQL**  
3. **Firewall bloqueando conexões**
4. **Configuração SSL incorreta**
5. **Problemas de rede Docker**

## 🔍 Diagnóstico

### 1. Teste de Conectividade Básica

```powershell
# No Windows (fora dos containers)
.\docker\scripts\test-db-connection.ps1
```

```bash
# No Linux/Mac (fora dos containers)
./docker/scripts/test-db-connection.sh
```

### 2. Teste Dentro do Container

```powershell
# Primeiro faça build dos containers
.\docker\scripts\build-staging.ps1

# Teste dentro do container
docker exec -it laravel-staging-app /usr/local/bin/test-container-db.sh
```

### 3. Verificar Logs do Container

```powershell
docker logs laravel-staging-app
docker logs laravel-prod-app
```

## ⚙️ Soluções

### Solução 1: Configurar PostgreSQL para Aceitar Conexões Externas

**No servidor PostgreSQL (177.44.248.74):**

1. **Editar postgresql.conf:**
```bash
sudo nano /etc/postgresql/13/main/postgresql.conf

# Encontre e modifique:
listen_addresses = '*'  # ou listen_addresses = '177.44.248.74'
port = 5432
```

2. **Editar pg_hba.conf:**
```bash
sudo nano /etc/postgresql/13/main/pg_hba.conf

# Adicione no final do arquivo:
# Permite conexões dos containers Docker
host    staging         postgres        172.16.0.0/12           md5
host    production      postgres        172.16.0.0/12           md5
host    staging         postgres        0.0.0.0/0               md5
host    production      postgres        0.0.0.0/0               md5
```

3. **Reiniciar PostgreSQL:**
```bash
sudo systemctl restart postgresql
```

### Solução 2: Configurar SSL Corretamente

**Opção A: Desabilitar SSL (para desenvolvimento)**

```bash
# Nos arquivos .env.staging e .env.production
DB_SSLMODE=disable
```

**Opção B: Configurar SSL adequadamente**

```bash
# Nos arquivos .env
DB_SSLMODE=require
# ou
DB_SSLMODE=prefer
```

### Solução 3: Verificar Firewall

**No servidor PostgreSQL:**

```bash
# Verificar se a porta está aberta
sudo ufw status
sudo ufw allow 5432

# Para iptables
sudo iptables -A INPUT -p tcp --dport 5432 -j ACCEPT
```

### Solução 4: Configuração Docker Network

Se ainda houver problemas, tente usar `host` network:

```yaml
# Em docker-compose.staging.yml
services:
  app:
    network_mode: "host"
    # ... resto da configuração
```

## 🧪 Testes Específicos

### Teste Manual de Conexão PostgreSQL

```powershell
# Teste direto com psql (se disponível)
psql "postgresql://postgres:postgres@177.44.248.74:5432/staging" -c "SELECT version();"
```

### Teste de Conectividade TCP

```powershell
# PowerShell
Test-NetConnection -ComputerName 177.44.248.74 -Port 5432

# Windows CMD
telnet 177.44.248.74 5432
```

### Teste via Container Temporário

```powershell
# Teste usando container PostgreSQL temporário
docker run --rm postgres:13 psql "postgresql://postgres:postgres@177.44.248.74:5432/staging" -c "SELECT 1;"
```

## 📋 Checklist de Verificação

- [ ] PostgreSQL está rodando no servidor externo
- [ ] Porta 5432 está aberta no firewall
- [ ] pg_hba.conf permite conexões da rede Docker
- [ ] postgresql.conf tem listen_addresses configurado
- [ ] Credenciais estão corretas nos arquivos .env
- [ ] Bancos 'staging' e 'production' existem
- [ ] Configuração SSL está adequada

## 🚀 Deploy Após Correção

```powershell
# Rebuild dos containers após mudanças
.\docker\scripts\build-staging.ps1
.\docker\scripts\build-production.ps1

# Ou faça deploy manual
docker exec -it laravel-staging-app /usr/local/bin/deploy.sh
```

## 📞 Suporte Adicional

Se o problema persistir, verifique:

1. **Logs do servidor PostgreSQL:**
   ```bash
   sudo tail -f /var/log/postgresql/postgresql-13-main.log
   ```

2. **Conectividade de rede:**
   ```bash
   traceroute 177.44.248.74
   ping 177.44.248.74
   ```

3. **Configurações específicas do provedor de nuvem** (se aplicável)
