# Crypto Rates API

API for cryptocurrency exchange rates (EUR/BTC, EUR/ETH, EUR/LTC) sourced from Binance.  
Rates are fetched automatically every 5 minutes.

## Requirements

- Docker
- Docker Compose

## Start

```bash
make start
```

## API

Swagger UI: [http://localhost:8080/api/doc](http://localhost:8080/api/doc)

### Get rates for the last 24 hours

```bash
curl "http://localhost:8080/api/rates/last-24h?pair=EUR/BTC"
```

### Get rates for a specific day

```bash
curl "http://localhost:8080/api/rates/day?pair=EUR/BTC&date=2026-04-30"
```

**Available pairs:** `EUR/BTC`, `EUR/ETH`, `EUR/LTC`

## Tests

```bash
make test
```
