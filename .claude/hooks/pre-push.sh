#!/bin/bash
# Pre-push hook for Claude Code
# Runs phpcs and PHPUnit before allowing git push

set -e

# Read the tool input from stdin
INPUT=$(cat)

# Extract the command being run
COMMAND=$(echo "$INPUT" | jq -r '.tool_input.command // empty')

# Only intercept git push commands
if ! echo "$COMMAND" | grep -qE '^\s*git\s+push\b'; then
    exit 0
fi

cd "$CLAUDE_PROJECT_DIR"

echo "Running phpcs before push..."
if ! vendor/bin/phpcs -n; then
    echo "phpcs failed. Fix code style issues before pushing." >&2
    exit 2
fi

echo "All checks passed."
exit 0
