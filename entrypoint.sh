#/bin/bash -e
echo "starting entrypoint.sh"

# Start the first process
# nohup npm run dev &

if [ -f /app/public/hot ] && [ "$APP_ENV" == 'production' ]; then
  echo "removing hot file"
  rm -rf /app/public/hot
fi

# Start the second process
php artisan serve --host=0.0.0.0 --port=8000