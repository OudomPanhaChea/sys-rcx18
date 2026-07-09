# Task
You are a senior full-stack engineer and AI engineer.

I need you to redesign and improve the "Report Assistant" page.

The goal is to make AI-generated support messages significantly more human, unique, and intelligent instead of producing generic responses.

Do NOT simply patch the current implementation.
Analyze the existing codebase first, understand the architecture, then implement the feature cleanly while keeping the code maintainable.

=========================================================
OBJECTIVES
=========================================================

The "Additional Message" column currently only has a "Generate" button.

I want to transform it into an intelligent AI message generation system.

The generated messages should:

• sound like they were written by a real human
• never sound robotic or AI-generated
• be natural
• polite
• persuasive
• concise
• not too short
• not too long
• professional
• unique every generation
• grammatically correct
• context-aware
• suitable for real support agents

Every generated message should be different while still maintaining the same meaning.

Avoid repeating sentence structures.

Avoid templates that always look the same.

=========================================================
AI TRAINING / SYSTEM PROMPT
=========================================================

Create a hidden internal system prompt (pre-prompt) that instructs the AI how to behave.

This system prompt should include rules like:

- Write naturally.
- Never sound like ChatGPT.
- Avoid exaggerated wording.
- Avoid unnecessary apologies.
- Be respectful.
- Be concise.
- Be persuasive without sounding demanding.
- Focus on facts.
- Use human tone.
- Do not over-explain.
- Never create walls of text.
- Keep paragraphs readable.
- Never repeat sentences.
- Produce unique wording every time.
- Keep the writing appropriate for customer support.

=========================================================
CATEGORY AWARENESS
=========================================================

The prompt should automatically adapt depending on:

Platform:
- TikTok
- Facebook
- Instagram

Issue:

Examples:

- Banned
- Suspended
- Disabled
- Warning
- Appeal
- Verification
- Management
- Login
- Hacked
- Security
- Monetization
- Community Guideline
- Ads
- Business
- Copyright
- Trademark
- Payment

etc.

Each platform has different support expectations.

Claude should build prompt templates that automatically adapt.

For example:

TikTok banned appeal should sound different from Instagram disabled account.

Facebook Business Manager should sound different from TikTok Community Guidelines.

=========================================================
USER CUSTOM PROMPT
=========================================================

Inside the "Additional Message" column, replace the current Generate button layout.

New UI:

-------------------------------------------------

[ Additional Prompt Input ]

(optional)

Placeholder:

"Add extra instruction for AI...
Example:
Mention that I already submitted multiple appeals."

-------------------------------------------------

[ Generate ]

-------------------------------------------------

The custom prompt should be appended to the hidden system prompt before sending to the AI.

Examples:

"Make it sound more urgent."

"Mention I have business documents."

"Use a more friendly tone."

"Do not mention my previous appeals."

If empty:

Use only the default prompt.

=========================================================
AI PROVIDER
=========================================================

Current provider:
Gemini

Improve the architecture so AI providers become modular.

Example:

providers/

gemini.ts

groq.ts

types.ts

index.ts

or any architecture you think is cleaner.

The application should support switching providers without rewriting logic.

=========================================================
FALLBACK AI
=========================================================

If Gemini:

- timeout
- fails
- rate limited
- invalid response
- network error

Automatically retry using Groq.

If Gemini succeeds:

Do not call Groq.

=========================================================
AI SETTINGS
=========================================================

Add a provider selector.

Example:

○ Gemini
○ Groq

Default:

Gemini

Checkbox:

☑ Auto fallback to Groq

If enabled:

Gemini
↓

Fail

↓

Groq

=========================================================
PROMPT BUILDER
=========================================================

Build prompts dynamically.

Instead of:

Huge hardcoded strings.

Create a prompt builder.

Example:

buildSupportPrompt({

platform,

issue,

username,

email,

project,

customInstruction,

})

The builder should assemble a structured prompt.

=========================================================
OUTPUT REQUIREMENTS
=========================================================

AI should ONLY return the final message.

No markdown.

No quotes.

No explanation.

No headings.

No "Here's your appeal".

No bullet points.

Only the final message ready to paste into the support form.

=========================================================
QUALITY RULES
=========================================================

Before returning the response, the AI should internally verify:

✓ sounds human

✓ natural

✓ concise

✓ platform appropriate

✓ issue appropriate

✓ no repeated phrases

✓ no grammar mistakes

✓ no AI wording

✓ no unnecessary apology

✓ persuasive

✓ readable

=========================================================
UI IMPROVEMENTS
=========================================================

Improve the Additional Message column.

Current:

[ Generate ]

New:

------------------------------------------------

Additional Prompt

[____________________________]

(optional helper text)

[ Generate ]

Loading...

Generating...

Regenerate

Copy

------------------------------------------------

Show loading state.

Disable button while generating.

Show success toast.

Show error toast.

=========================================================
CODE QUALITY
=========================================================

Requirements:

- TypeScript strict
- Reusable components
- No duplicated code
- Clean architecture
- Proper error handling
- Async/await
- Modular prompt builder
- Modular AI providers
- Easy to add future providers (OpenAI, Claude, DeepSeek, etc.)
- Follow existing project style

=========================================================
DELIVERABLES
=========================================================

Implement:

1. Smarter AI prompt system
2. Platform-aware prompting
3. Issue-aware prompting
4. Hidden system prompt
5. User custom prompt input
6. Modular AI provider architecture
7. Gemini provider
8. Groq provider
9. Automatic fallback
10. Provider selector
11. Better UI for Additional Message
12. Loading/error/success states
13. Clean, maintainable code
14. Refactor existing AI generation logic where necessary without breaking current functionality.

Take time to analyze the project first, then implement the feature with production-quality code rather than the quickest solution.