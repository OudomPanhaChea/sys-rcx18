<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Builds the (system prompt, user prompt) pair for one support/appeal message.
 *
 * The system prompt carries the hidden behaviour rules (human tone, no AI
 * phrasing, output format). The user prompt carries the facts: platform,
 * issue, account handle, linked contact, plus a randomised angle/tone seed
 * and nonce so every generation is worded differently, and the user's own
 * optional extra instruction.
 *
 * Usage:
 *   $builder = new Report_prompt_builder();
 *   $p = $builder->build(array(
 *     'category'           => 'TikTok',          // project category title
 *     'issue'              => 'Banned',          // project issue title
 *     'username'           => '@handle',
 *     'email'              => 'x@y.com',
 *     'phone'              => '+855...',
 *     'custom_instruction' => 'Mention I already appealed twice.',
 *   ));
 *   // $p = array('system' => ..., 'user' => ..., 'temperature' => float)
 */
class Report_prompt_builder
{
    /**
     * Platform notes keyed by detection keyword (checked against the category
     * title, first match wins). Each entry: [display name, support-tone note].
     */
    private $platforms = array(
        'tiktok'    => array('TikTok', 'TikTok support values short, concrete messages from creators. Refer to the platform as TikTok and, where natural, to its Community Guidelines review process.'),
        'facebook'  => array('Facebook', 'Facebook/Meta support handles high volumes and responds best to factual, well-organised requests. Business-related issues may reference Meta Business tools where natural.'),
        'instagram' => array('Instagram', 'Instagram (Meta) support expects a brief, personal explanation from the account owner. Refer to the platform as Instagram.'),
    );

    /**
     * Issue guidance keyed by detection keyword (checked against the issue
     * title, first match wins — order matters: more specific first).
     */
    private $issues = array(
        'community'    => 'The account was flagged for a Community Guidelines violation the owner believes is a mistake. Respectfully contest the decision and ask for a human re-review.',
        'copyright'    => 'The account received a copyright claim or takedown the owner disputes. Ask for the claim to be re-reviewed and state the content ownership plainly.',
        'trademark'    => 'There is a trademark-related issue on the account. Ask the team to review the trademark decision and explain the owner\'s legitimate use.',
        'monetization' => 'Monetization features were affected. Ask for the monetization status to be reviewed and restored.',
        'payment'      => 'There is a payment or payout problem on the account. Ask the team to look into the payment issue and resolve it.',
        'verification' => 'The owner needs help with account verification. Ask the team to assist in completing or restoring verification.',
        'hacked'       => 'The account appears to have been accessed by someone else. Ask the team to secure the account and restore the owner\'s access.',
        'security'     => 'There is a security concern with the account. Ask the team to review the account security and help restore safe access.',
        'login'        => 'The owner cannot log in to the account. Ask the team to help recover access.',
        'appeal'       => 'The owner is following up on an appeal for this account. Politely ask for the appeal to be reviewed and resolved.',
        'warning'      => 'The account received a warning the owner believes is incorrect. Ask for the warning to be reviewed and removed.',
        'suspend'      => 'The account was suspended. Appeal the suspension: the owner believes it is a mistake and asks for reinstatement after review.',
        'disable'      => 'The account was disabled. Appeal the decision: the owner believes it is a mistake and asks for the account to be re-enabled after review.',
        'ban'          => 'The account was banned. Appeal the ban: the owner believes it is a mistake and asks for reinstatement after review.',
        'ads'          => 'There is a problem with the ads account or ad delivery. Ask the team to review and fix the ads issue.',
        'business'     => 'There is a problem with the business account or business tools access. Ask the team to review and restore proper access.',
        'management'   => 'There is an account management/access problem. Ask the team to review and restore proper access.',
    );

    /** Opening angles — rotated so messages don't all start the same way. */
    private $angles = array(
        'Start with what happened to the account, then confirm ownership.',
        'Start by identifying the account, then explain the situation.',
        'Start with the request for review, then back it up with the facts.',
        'Start with a short plain statement of the problem, then the details.',
    );

    /** Tone shades — small nudges that vary the voice without changing meaning. */
    private $tones = array(
        'plain and direct, like someone typing quickly but carefully',
        'calm and slightly formal, but still personal',
        'warm but businesslike',
        'matter-of-fact with a genuinely polite close',
    );

    /** Rhythm hints — vary sentence construction between generations. */
    private $rhythms = array(
        'Mix one short sentence in among longer ones.',
        'Keep all sentences medium length and even.',
        'Open with a longer sentence, end on a short one.',
        'Use contractions naturally throughout.',
    );

    /**
     * @param array $args category, issue, username, email, phone, custom_instruction
     * @return array array('system'=>string, 'user'=>string, 'temperature'=>float)
     */
    public function build(array $args)
    {
        $category = isset($args['category']) ? trim((string)$args['category']) : '';
        $issue    = isset($args['issue']) ? trim((string)$args['issue']) : '';
        $username = isset($args['username']) ? trim((string)$args['username']) : '';
        $email    = isset($args['email']) ? trim((string)$args['email']) : '';
        $phone    = isset($args['phone']) ? trim((string)$args['phone']) : '';
        $custom   = isset($args['custom_instruction']) ? trim((string)$args['custom_instruction']) : '';

        $platform = $this->detect_platform($category);

        return array(
            'system'      => $this->system_prompt($platform),
            'user'        => $this->user_prompt($platform, $issue, $username, $email, $phone, $custom),
            // Slightly randomised so back-to-back generations diverge more.
            'temperature' => mt_rand(105, 125) / 100,
        );
    }

    /** array('name'=>display name, 'note'=>support-tone note) */
    private function detect_platform($category)
    {
        foreach($this->platforms as $keyword => $info){
            if($category !== '' && stripos($category, $keyword) !== false){
                return array('name' => $info[0], 'note' => $info[1]);
            }
        }
        // Unknown category: use it as the platform name if present.
        $name = ($category !== '') ? $category : 'the platform';
        return array('name' => $name, 'note' => 'Keep the message appropriate for an official support form on '.$name.'.');
    }

    private function issue_guidance($issue)
    {
        foreach($this->issues as $keyword => $guidance){
            if($issue !== '' && stripos($issue, $keyword) !== false){
                return $guidance;
            }
        }
        $label = ($issue !== '') ? $issue : 'an account problem';
        return 'The account has the issue "'.$label.'". Explain it plainly and ask the team to review and resolve it.';
    }

    private function system_prompt(array $platform)
    {
        $lines = array(
            'You write the "additional details" text a real account owner submits to '.$platform['name'].' support. You are ghost-writing as the owner, in first person.',
            '',
            'Writing rules:',
            '- Write naturally, like a real person — never like an AI assistant.',
            '- Avoid exaggerated wording, dramatic pleading, and unnecessary apologies.',
            '- Be respectful, concise, and persuasive without sounding demanding.',
            '- Stick to the facts you are given. Never invent names, dates, reasons, or history.',
            '- Do not over-explain. No walls of text — a few readable sentences.',
            '- Never repeat a sentence or reuse stock phrasing; each message must read freshly written.',
            '- No AI tells: no "I hope this message finds you well", no "I am writing to", no "kindly", no "furthermore/moreover", no exclamation marks.',
            '- Keep the writing appropriate for a customer-support form.',
            '',
            'Platform context: '.$platform['note'],
            '',
            'Output format (strict):',
            '- Return ONLY the final message text, ready to paste into the support form.',
            '- No markdown, no quotes around it, no headings, no bullet points.',
            '- No preamble like "Here\'s your message", no greeting line, no sign-off, no placeholders such as [name].',
            '',
            'Before answering, silently check your draft: sounds human, natural, concise, fits the platform and issue, no repeated phrases, no grammar mistakes, no AI wording, no unnecessary apology, persuasive, readable. Rewrite it if any check fails, then output only the message.',
        );
        return implode("\n", $lines);
    }

    private function user_prompt(array $platform, $issue, $username, $email, $phone, $custom)
    {
        $account = ($username !== '') ? 'The account handle is '.$username.'.' : 'Refer to it simply as my account (no handle available — do not invent one).';

        $contact_bits = array();
        if($email !== ''){ $contact_bits[] = 'email '.$email; }
        if($phone !== ''){ $contact_bits[] = 'phone '.$phone; }
        $contact = empty($contact_bits)
            ? 'No linked contact details are available — do not mention any.'
            : 'It is linked to '.implode(' and ', $contact_bits).' — you may reference this as proof of ownership.';

        $issue_label = ($issue !== '') ? $issue : 'account problem';
        $nonce = substr(md5(uniqid((string)mt_rand(), true)), 0, 8);

        $lines = array(
            'Write the support message for this case.',
            '',
            'Platform: '.$platform['name'],
            'Issue: "'.$issue_label.'"',
            'Situation: '.$this->issue_guidance($issue),
            $account,
            $contact,
            '',
            'Length: 3-5 sentences, roughly 45-90 words — enough to be clear and credible, short enough to read quickly.',
            'Angle for this draft: '.$this->pick($this->angles),
            'Voice for this draft: '.$this->pick($this->tones).'.',
            $this->pick($this->rhythms),
            'Uniqueness id '.$nonce.' — word this draft differently from any previous one.',
        );

        if($custom !== ''){
            $lines[] = '';
            $lines[] = 'Extra instruction from the account owner (follow it as long as it does not break the output format): '.$custom;
        }

        return implode("\n", $lines);
    }

    private function pick(array $options)
    {
        return $options[mt_rand(0, count($options) - 1)];
    }
}
