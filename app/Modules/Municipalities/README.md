# Municipalities module

**Status: structure prepared; feature implementation pending.**

Owns the Antique municipality directory and municipality-based discovery filters.

Use `Controllers` for directory requests, `Services` for discovery rules,
`Repositories` for directory queries, `Requests` for filter validation, and
`Views` for directory templates. Other modules reference municipality identifiers
rather than copying directory logic.
See [module conventions](../../../docs/FILE_MANAGEMENT.md).
