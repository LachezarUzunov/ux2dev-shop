## Public Partner Orders API — Design Summary

# Idempotency
* Required Idempotency-Key header
* Unique (partner_id + key)
* Request hash validation
* Cached response replay
* Redis distributed lock + DB uniqueness

# Rate Limiting

* Token bucket algorithm
* Global + IP + Partner layers
* Burst traffic allowed up to bucket capacity
* Structured 429 responses with headers
* Smooth refill rate
* Redis-backed
* Fail-open strategy

# Security
* API key + secret
* HMAC signature
* Timestamp replay window
* Encrypted secrets
* Signature verification

# Observability
* Request correlation IDs
* Structured logs
* Partner context logging
* Rate limit events
* Idempotency replay logs
* Order latency measurement

# Failure Handling
* DB transaction + retry on deadlock
* Idempotent writes prevent duplicates
* Downstream calls async (design)
* Guarantees at-least-once without double-create

# Redis Failure Strategy
Limiter **fails open** if Redis is unavailable:
* Requests continue
* Warning logs emitted
* Availability prioritized over strict throttling
