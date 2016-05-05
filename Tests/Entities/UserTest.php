<?php

    namespace Tests\Common;

    use Idno\Core\Template;

    class UserTest extends \Tests\KnownTestCase {

        function testPronounDisplay() {
            $user = $this->user();
            $user->profile['pronoun-nominative'] = 'xe';
            $user->profile['pronoun-oblique'] = 'xem';
            $user->profile['pronoun-possessive'] = 'xyrs';

            \Idno\Core\Idno::site()->config()->enable_pronouns = true;
            $profile = (new Template())->__(['user' => $user])->draw('entity/User');
            $pronoun = (new Template())->__(['pronouns' => $user->getPronoun()])->draw('entity/User/profile/pronouns');
            $this->assertTrue((bool) substr_count($profile, $pronoun), 'Pronouns should display when enable_pronouns is true');

            \Idno\Core\Idno::site()->config()->enable_pronouns = false;
            $profile = (new Template())->__(['user' => $user])->draw('entity/User');
            $pronoun = (new Template())->__(['pronouns' => $user->getPronoun()])->draw('entity/User/profile/pronouns');
            $this->assertFalse((bool) substr_count($profile, $pronoun), 'Pronouns should not display when enable_pronouns is false');
        }

    }