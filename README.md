# WordPress Plugin development with PHP and JavaScript incl. project management 
[yourdailygerman.com/dictionary](https://yourdailygerman.com/dictionary)

## summary
[yourdailygerman.com](https://yourdailygerman.com) is an online blog for learners of German as a Foreign Language specialising in long-form, analytical-style teaching material. 

The project delivered a searchable dictionary of key terms and an accompanying user interface for their entry as a plugin to the author's existing WordPress website. 

### technologies
* WordPress
* PHP
* SQL
* JavaScript
* HTML/CSS

## Planning
### Problem Statement
[yourdailygerman](https://yourdailygerman.com) is losing the attention of its readers in the interim between each new publication as they leave the site in search of translations and example sentences whilst studying German.
Although the long-form, analytical-style articles published here are extremely thorough and informative, the website does not offer a convenient way to quickly reference the translations and example sentences presented within them.
Readers prefer online dictionaries like dict.cc, leo.org and linguee.com for their convenience and accessibility.

### Vision Statement
yourdailygerman is guided by the author’s belief that success in language learning comes not from the effort one puts into learning the language but from the degree to which one is genuinely interested in, intrigued by and has a curiosity for the language itself.
This deeper curiosity is the key driving force behind prolonged engagement with a foreign language- the one true means to achieving competency.

### Mission Statement
My mission for this product, in continuing with the overarching vision of the blog, is to provide a tool to help prolong readers' engagement with yourdailygerman and thus their target foreign language.
The tool aims to address the shortcomings outlined in the Problem Statement by facilitating the quick retrieval of the words and phrases presented throughout the blog accompanied by their respective translations and example sentences for study purposes.

### Success Statement
Success would be delivering a product that
* retrieves words and phrases and their respective translations and
example sentences,
* increases returning traffic to the website in the interim between each new publication.
* facilitates other ways for the author to leverage the data in the future,

### User Stories
#### As a reader, I want to see information about words and phrases that have been presented in the blog
* I should be able to view a page that contains all of the relevant information for a single word or phrase.
* I should be able to bookmark the page or share the URL with others.
* I should be able to ask the author questions about the word/phrase or comment on my experience learning the word/phrase.
* I should be able to search for other words or phrases from the page.

#### As a reader, I want a list of all the words presented in a given article at the bottom of the article so I can quickly review what I’ve just read.
* When I am finished reading an article, I should be presented with a list of all of the words or phrases that have been presented in the article. 
* It could be titled, glossary or vocabulary.
* It should be displayed after the article content but before the comment section.
* Each entry should have a link to view a dedicated page about the word or phrase where I can see more information.

#### As a reader, I would like to have the option to browse a list of words from all articles looking for something that takes my fancy because it can be difficult to know which article to read next
* The list should be in alphabetical order
* The list should load and respond quickly 
* I should be able to view the list on my mobile device
* I should be able to scroll endlessly through the list 

#### As a reader, I want the core idea(s) of each word summarised in English translations wherever words and phrases are shown so that I don't need to read the whole article again when I am struggling to recall what was presented
* Several English translations should be given per idea to triangulate the sense of it. e.g.) 
verlassen => to leave sb behind, to desert sb, to abandon sb. which is different from 
verlassen => to leave sb, to break up w/ sb, to dump sb
* I should be able to see notes on each idea to confirm things not necessarily expressed by the translations. e.g.) colloquial, formal, old-fashioned, explicit, etc

#### As a reader, I would like to see alternative forms of words wherever words are shown because it is not always obvious that a word is, for example, the preterit of a verb
This should include: 
* the plural form and gender of nouns
* the three principle-parts of verbs
this does not have to include:
* a full case-table
* weak masculine nouns
These should at least be shown on individual entry pages, in the glossary of articles and on the dictionary page

#### As a reader, I would like to see information on related words wherever words or phrases are shown because the extra context can help to consolidate understanding
This should include:
* the root of prefix-verbs. e.g.) abfahren is related to fahren
* verbs that nouns and adjectives are often derived from. e.g.) Abfahrt and abgefahren are related to abfahren
* words that form part of a phrase e.g.) einen fahren lassen contains fahren

#### As a reader, I would like to see which (other) articles a word or phrase is mentioned in so I can read more about it and/or its related words
* On individual entry pages, all articles that the word or phrase is mentioned in should be listed and include links to the view them.
* In the glossary on article pages, this should only be shown if the word or phrase is mentioned in any other article(s).
* This should not be shown on the dictionary page.

#### As a reader, I would like to see grammar information for words and phrases so I can quickly see without referring to examples if a verb, for example, is transitive or intransitive, what case its object should use and so on.
* These should be associated with the ideas/translations of the word or phrase because a verb, for example, may carry different meanings in its infinitive or reflexive form. e.g.)
anstellen => to employ sb
sich anstellen => to make a fuss
* And should include the case of objects and/or required prepositions. e.g.)
aussetzen => to expose sth/sb to sth (etwas/jmdn(akk) etwas(dat) aussetzen)

#### As a reader, I would like to see the grammatical type of a word wherever words are shown to avoid confusing, for example, an adjective for a verb
* This should be front and centre/directly next to the word and shown everywhere words are.
* Types should include at least: noun, verb, adjective and adverb 
* but could be extended to include phrase, pronoun, particle, preposition, conjunction, interjection and so on.

#### As a reader, I would like audio recordings of words and phrases everywhere words and phrases are shown to improve listening comprehension and pronunciation
* These could be shown everywhere words and phrases are.
* These should be click-to-play and shouldn’t require navigating away from the current page.
* I should be able to download these recordings and use them in other study tools.

#### As a reader, I would like to see a preview of the article(s) in which a word or phrase is mentioned to help decide which I am most interested in reading next
* This need only be shown on the individual entry pages.
* It should include the article’s title, image, the blurb and a list of the other words and phrases mentioned in the article.
* This could be collapsible for aesthetic purposes.

#### As a reader, I would like to see all of the example sentences for a word or phrase in one place because it can take a long time to find them especially if the word is mentioned in several articles.
* These should be shown on the individual entry page. This would be too much info to show anywhere else
* When the same word can be used to express multiple different ideas, it should be clear which idea the sentences is an example of. 
* The example sentences should be provided in both English and German.
* The sentences should also have audio recordings

#### As the author, I would like to add entries (words and phrases and their respective information, translations and so on) from the edit post screen so that my workflow is not interrupted.
* Preferably in the main column of the edit post screen, not in the sidebar.
* I should be able to add all necessary information to entries from this screen including word type, alternate forms, grammar information, to which other entry it relates, translations and so on.
* I should be able to add as many translations to an entry as is necessary.
* I should be able to save changes made here without saving or publishing the article.
* In case I forget to save, changes made here should also be saved when I save the article.
* I should be able to save or publish the article without entering any entries.
* I should be warned if I am trying to create an entry that already exists elsewhere.
* If an entry exists elsewhere, I should be able to import it into this article.
* If I edit an entry that is mentioned in multiple articles, the changes should be seen everywhere.

#### As an editor, I want a way to import entries from the CSV files we have created to quickly populate the project with some content
* Words and phrases and their respective translations have been specifically delimited from each other in the CSV files.
* Word types, verb prefixes, the plural form and the gender of nouns, translation notes and other useful information should be programmatically extracted from the fields in the CSV where possible and HTML input fields should be auto populated accordingly. Great… thanks… a half-assed job. Don’t worry, the computer will magically fix this /s.
* Entries and their associated information should be imported from the CSV files into the client-side user interface to be proofed and edited. They should not be loaded into the database directly.

#### As a reader, I want the option to search for words and phrases I am looking for because there are many.
* This should be possible from the dictionary and individual entry pages.
* The dictionary and search results pages should be the same/indistinguishable from each other.
* The search should be fast.
* The results should be ordered by relevance. 
* I should be able to scroll to the bottom of the results without having to load subsequent pages.
* I should be able to bookmark and share search results with others.

#### As a reader, I would like to be able to search for English words/phrases and have the German equivalent returned to avoid common confusions with ‘false friends'
* On the search page, I should be able to search not just for the German words and phrases presented in the articles, but also for English words and phrases that I am looking to translate into German.
* I should also be able to limit the search results to German or English. So that the German word der Art (in English: type) is not shown when searching for the English word art (in German: die Kunst)

#### As a reader, I would like to search for words and limit the results to certain word types to avoid common confusions
* I should be able to limit search results to one or more word-types.
* I should also be able to click on the word type to view all entries of that type without limiting the results to a search term.

#### As a reader, I would a flashcards web app to test their retention of the words they’ve learned reading the blog
* I should be able to add example sentences to a deck of flashcards.
* I should be able to see if a sentence is in one of my study decks.
* I should be able to remove flashcards from a deck.
* I should be able to study the flashcards on the website.
* The backside of the flashcard should include audio recordings, grammar information and hints and tips for use and/or recollection. 
* The flashcard system should follow a spaced repetition algorithm showing me the card again at different intervals based on my recollection.

### Project Scope
#### Included
* graphical user interface for Administrators with import tool for CSV files
* Article-page glossary
* Site-wide dictionary
* Individual entry pages
* Search
#### Not included
* Add study notes to articles
* Study words and phrases as flashcards

### Rough Order of Value
1. Provide the author with an interface to add words and phrases
2. Show readers a glossary of words and phrases presented in articles
3. Show readers a dictionary of words and phrases presented site-wide
4. Provide translations to users wherever words and phrases are shown
5. Offer search directing traffic to older posts
6. Provide the author with a tool to populate the dictionary faster
7. Expand entry implementation to include conveniences for users eg. audio
8. Expand search capabilities to reflect updated entries
9. Provided users with individual pages for each entry with audio and example sentences

## Prototyping
I will use the WordPress Metadata API (custom fields) and the Advanced Custom Fields (ACF) plugin to provide a crude but functional prototype of the core functionality.
The Metadata API uses an EAV data model to facilitate associating an unknown variety of user-defined data to posts and the API itself allows for quick data definition and manipulation. As the name suggests, ACF facilitates associating more complex data to post than is possible with the Metadata API. 
The flexibility and convenience of these two tools, especially in combination, will allow for rapid prototyping of the proposed solution for demonstration purposes. Once approved, however, this approach will need to be abandoned in favour of custom database tables for several reasons. These are outlined below.

## Solution
Significant consideration regarding data access and storage will be required in taking this project from prototype to solution. 

All of the requirements outlined in planning constitute a problem finding translations and example sentences quickly. Search performance, therefore, will be of significant importance when delivering any credible solution as well as the ability to view/retrieve individual entries- two things that the ACF and WordPress Metadata API approach proposed for prototyping will simply not achieve.

Being at the heart of WordPress, Posts are deeply integrated into the platform. WordPress Custom Post Types allow developers to leverage this integration whilst differentiating their custom posts from the default ones. Creating a custom post type for my dictionary entries will provide out-of-the-box graphical user interfaces for CRUD operations, quick integration with site menus, an archive page, individual pages for each dictionary entry, hierarchical relationships between entries, post taxonomies for grouping similar entries and most importantly access to the WordPress Post Query API.

The translation and example sentences associated with each dictionary entry, although essentially just metadata, are also not appropriate candidates for the Metadata API because of their many-to-many relationship with entries. One word may have many translations/meanings and one meaning may be expressed by many words. Similarly, many example sentences may be provided for one translation and one sentence may be used as an example for many meanings. EAV data models are simply not intended to support such relationships. Moreover, this data must be searchable and performant. Custom tables should be added to the WordPress database to achieve this.

The WordPress Post Query class assembles SQL queries clause-by-clause from parameters passed on construction. This can be done explicitly or left to WordPress to create and based on the requested URL. In the latter approach, developers can hook into the creation process altering the produced query as needed. This technique should be used on the dictionary archive page and individual entry pages to JOIN the translation and sentence tables to the query.
