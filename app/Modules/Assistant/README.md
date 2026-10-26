# Assistant module

**Status: structure prepared; feature implementation pending.**

Owns Gemini integration, community-information retrieval, grounded answers,
and source links. `GEMINI_API_KEY` is reserved configuration and currently unused.

Keep chat handlers in `Controllers`, retrieval and answer rules in `Services`,
knowledge queries in `Repositories`, message validation in `Requests`, and
assistant templates in `Views`. Answers must cite available contributions and
communicate missing confirmation. Private file content must not be included in
shared retrieval. See [module conventions](../../../docs/FILE_MANAGEMENT.md).
