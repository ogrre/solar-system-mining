#!/bin/bash

# Script pour configurer la protection de branche via GitHub CLI
# Usage: ./scripts/setup-branch-protection.sh

gh api repos/ogrre/solar-system-mining/branches/main/protection \
  --method PUT \
  --field required_status_checks='{"strict":true,"contexts":["laravel-tests","SonarCloud Code Analysis"]}' \
  --field enforce_admins=true \
  --field required_pull_request_reviews='{"required_approving_review_count":1,"dismiss_stale_reviews":true}' \
  --field restrictions=null \
  --field required_linear_history=true \
  --field allow_force_pushes=false \
  --field allow_deletions=false

echo "✅ Branch protection configured for main branch"