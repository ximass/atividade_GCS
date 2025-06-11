#!/bin/bash

# Database Backup Script for CI/CD
# This script creates a backup of the database before deployment

set -e

# Configuration
BACKUP_DIR="/tmp/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
ENVIRONMENT=${1:-staging}

# Create backup directory
mkdir -p $BACKUP_DIR

echo "Starting database backup for environment: $ENVIRONMENT"

if [ "$ENVIRONMENT" = "production" ]; then
    DB_HOST=${PROD_DB_HOST:-"177.44.248.74"}
    DB_PORT=${PROD_DB_PORT:-"5432"}
    DB_NAME=${PROD_DB_NAME:-"production"}
    DB_USER=${PROD_DB_USER:-"postgres"}
    BACKUP_FILE="$BACKUP_DIR/prod_backup_$TIMESTAMP.sql"
elif [ "$ENVIRONMENT" = "staging" ]; then
    DB_HOST=${STAGING_DB_HOST:-"177.44.248.74"}
    DB_PORT=${STAGING_DB_PORT:-"5432"}
    DB_NAME=${STAGING_DB_NAME:-"staging"}
    DB_USER=${STAGING_DB_USER:-"postgres"}
    BACKUP_FILE="$BACKUP_DIR/staging_backup_$TIMESTAMP.sql"
else
    echo "Invalid environment: $ENVIRONMENT"
    exit 1
fi

# Create database backup
echo "Creating backup: $BACKUP_FILE"
PGPASSWORD=${DB_PASSWORD} pg_dump \
    -h $DB_HOST \
    -p $DB_PORT \
    -U $DB_USER \
    -d $DB_NAME \
    --verbose \
    --clean \
    --no-owner \
    --no-privileges \
    -f $BACKUP_FILE

# Compress backup
gzip $BACKUP_FILE
COMPRESSED_BACKUP="${BACKUP_FILE}.gz"

echo "Backup completed: $COMPRESSED_BACKUP"
echo "Backup size: $(du -h $COMPRESSED_BACKUP | cut -f1)"

# Keep only last 5 backups
echo "Cleaning old backups..."
cd $BACKUP_DIR
ls -t ${ENVIRONMENT}_backup_*.sql.gz | tail -n +6 | xargs -r rm --

echo "Backup process completed successfully!"
echo "BACKUP_FILE=$COMPRESSED_BACKUP" >> $GITHUB_OUTPUT
