## AWS Cognito PHP Demo
Sample .env file
```sh
REGION=eu-central-1
CLIENT_ID=1a2b3c4d5e6f7g8h0i
CLIENT_SECRET=1a2b3c4d5e6f7g8h0i1a2b3c4d5e6f7g8h0i1a2b3c4d5e6f7g8h0i
USERPOOL_ID=eu-central-1_ABCDEFG
#AWS_ACCESS_KEY_ID=(optional)
#AWS_SECRET_ACCESS_KEY=(optional)
```

Running demo
```sh
AWS_ACCESS_KEY_ID=$(aws --profile default configure get aws_access_key_id)
AWS_SECRET_ACCESS_KEY=$(aws --profile default configure get aws_secret_access_key)

docker run --rm -ti \
   --env-file .env \
   -e AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID \
   -e AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY \
   -v "$PWD:/var/www" \
   -p 8000:80 php:8.1-cli \
   php -S 0.0.0.0:80 -t "/var/www/public"
```