# Moderation module

**Status: structure prepared; feature implementation pending.**

Owns reports, correction suggestions, review decisions, outdated information
flags, and verification labels.

Use `Controllers` for moderator actions, `Services` for review workflows,
`Repositories` for persistence/audit records, `Requests` for validation, and
`Views` for moderation screens. Moderator privileges must come from trusted
server-managed roles, never user-editable metadata. A community review does not
mean government approval. See [module conventions](../../../docs/FILE_MANAGEMENT.md).
