# Questions module

**Status: structure prepared; feature implementation pending.**

Owns community questions, answers, and helpful votes.

Use `Controllers` for HTTP actions, `Services` for answer and voting rules,
`Repositories` for persistence, `Requests` for validation, and `Views` for
question/answer templates. Enforce author permissions and prevent duplicate votes
through database constraints and policy tests.
See [module conventions](../../../docs/FILE_MANAGEMENT.md).
