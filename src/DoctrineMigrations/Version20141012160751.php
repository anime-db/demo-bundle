<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\DevBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AnimeDb\Bundle\AppBundle\Util\Filesystem;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Name;
use AnimeDb\Bundle\CatalogBundle\Entity\Source;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141012160751 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * Filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Entity manager
     *
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * Target dir
     *
     * @var string
     */
    protected $target;

    /**
     * Source dir
     *
     * @var string
     */
    protected $source;

    /**
     * Set container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->abortIf(is_null($container), 'Requires DI container');

        $this->fs = $container->get('filesystem');
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->target = $container->getParameter('kernel.root_dir').'/../web/media/example/';
        $this->source = $container->get('kernel')->locateResource('@AnimeDbDevBundle/Resource/private/images/');
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::up()
     */
    public function up(Schema $schema)
    {
        // copy images for example items
        $this->fs->mirror($this->source, $this->target);

        // create storage
        $storage = (new Storage())
            ->setDescription('Storage on local computer')
            ->setName('Local')
            ->setPath(Filesystem::getUserHomeDir())
            ->setType(Storage::TYPE_FOLDER);
        $this->em->persist($storage);

        // create items
        $this->persist($this->getItemOnePiece($storage));
        $this->persist($this->getItemFullmetalAlchemist($storage));
        $this->persist($this->getItemSpiritedAway($storage));
        $this->em->flush();
    }
    /**
     * (non-PHPdoc)
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::down()
     */
    public function down(Schema $schema)
    {
        // remove images for example items
        $this->fs->remove($this->target);
    }

    /**
     * Persist item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     */
    protected function persist(Item $item)
    {
        $this->em->persist($item);
        foreach ($item->getNames() as $name) {
            $this->em->persist($name);
        }
        foreach ($item->getSources() as $source) {
            $this->em->persist($source);
        }
    }

    /**
     * Get country
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Country
     */
    protected function getCountry($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Country')->find($name);
    }

    /**
     * Get type
     *
     * @param string $id
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Type
     */
    protected function getType($id)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Type')->find($id);
    }

    /**
     * Get studio
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Studio
     */
    protected function getStudio($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Studio')->findBy(['name' => $name]);
    }

    /**
     * Get genre
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Genre
     */
    protected function getGenre($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Genre')->findBy(['name' => $name]);
    }

    /**
     * Get One Piece item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected function getItemOnePiece(Storage $storage)
    {
        return (new Item())
            ->setCountry($this->getCountry('JP'))
            ->setCover('example/one-piece.jpg')
            ->setDatePremiere(new \DateTime('1999-10-20'))
            ->setDuration(25)
            ->setEpisodesNumber('602+')
            ->setFileInfo('+ 6 спэшлов')
            ->setName('One Piece')
            ->setPath($storage->getPath().'One Piece (2011) [TV]'.DIRECTORY_SEPARATOR)
            ->setStorage($storage)
            ->setSummary(
                'Последние слова, произнесенные Королем Пиратов перед казнью, вдохновили многих: «Мои сокровища? Коли '
                .'хотите, забирайте. Ищите – я их все оставил там!». Легендарная фраза Золотого Роджера ознаменовала '
                .'начало Великой Эры Пиратов – тысячи людей в погоне за своими мечтами отправились на Гранд Лайн, '
                .'самое опасное место в мире, желая стать обладателями мифических сокровищ... Но с каждым годом '
                .'романтиков становилось все меньше, их постепенно вытесняли прагматичные пираты-разбойники, которым '
                .'награбленное добро было куда ближе, чем какие-то «никчемные мечты». Но вот, одним прекрасным днем, '
                .'семнадцатилетний Монки Д. Луффи исполнил заветную мечту детства - отправился в море. Его цель - ни '
                .'много, ни мало стать новым Королем Пиратов. За достаточно короткий срок юному капитану удается '
                .'собрать команду, состоящую из не менее амбициозных искателей приключений. И пусть ими движут '
                .'совершенно разные устремления, главное, этим ребятам важны не столько деньги и слава, сколько куда '
                .'более ценное – принципы и верность друзьям. И еще – служение Мечте. Что ж, пока по Гранд Лайн '
                .'плавают такие люди, Великая Эра Пиратов всегда будет с нами!'
            )
            ->setStudio($this->getStudio('Toei Animation'))
            ->setType($this->getType('tv'))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Comedy'))
            ->addGenre($this->getGenre('Shounen'))
            ->addGenre($this->getGenre('Fantasy'))
            ->addName((new Name())->setName('Ван-Пис'))
            ->addName((new Name())->setName('Одним куском'))
            ->addName((new Name())->setName('ワンピース'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=836'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=69'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/21/'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/350/time'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=162790'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/One_Piece'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/One_Piece'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/ONE_PIECE_%28%E3%82%A2%E3%83%8B%E3%83%A1%29'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=731'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=803'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/21-one-piece'));
    }

    /**
     * Get Fullmetal Alchemist item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected function getItemFullmetalAlchemist(Storage $storage)
    {
        return (new Item())
            ->setCountry($this->getCountry('JP'))
            ->setCover('example/fullmetal-alchemist.jpg')
            ->setDateEnd(new \DateTime('2004-10-02'))
            ->setDatePremiere(new \DateTime('2003-10-04'))
            ->setDuration(25)
            ->setEpisodes(
'1. To Challenge the Sun (04.10.2003, 25 мин.)
2. Body of the Sanctioned (11.10.2003, 25 мин.)
3. Mother (18.10.2003, 25 мин.)
4. A Forger`s Love (25.10.2003, 25 мин.)
5. The Man with the Mechanical Arm (01.11.2003, 25 мин.)
6. The Alchemy Exam (08.11.2003, 25 мин.)
7. Night of the Chimera`s Cry (15.11.2003, 25 мин.)
8. The Philosopher`s Stone (22.11.2003, 25 мин.)
9. Be Thou for the People (29.11.2003, 25 мин.)
10. The Phantom Thief (06.12.2003, 25 мин.)
11. The Other Brothers Elric, Part 1 (13.12.2003, 25 мин.)
12. The Other Brothers Elric, Part 2 (20.12.2003, 25 мин.)
13. Fullmetal vs. Flame (27.12.2003, 25 мин.)
14. Destruction`s Right Hand (10.01.2004, 25 мин.)
15. The Ishbal Massacre (17.01.2004, 25 мин.)
16. That Which Is Lost (24.01.2004, 25 мин.)
17. House of the Waiting Family (31.01.2004, 25 мин.)
18. Marcoh`s Notes (07.02.2004, 25 мин.)
19. The Truth Behind Truths (14.02.2004, 25 мин.)
20. Soul of the Guardian (21.02.2004, 25 мин.)
21. The Red Glow (28.02.2004, 25 мин.)
22. Created Human (06.03.2004, 25 мин.)
23. Fullmetal Heart (13.03.2004, 25 мин.)
24. Bonding Memories (20.03.2004, 25 мин.)
25. Words of Farewell (27.03.2004, 25 мин.)
26. Her Reason (03.04.2004, 25 мин.)
27. Teacher (10.04.2004, 25 мин.)
28. All is One, One is All (17.04.2004, 25 мин.)
29. The Untainted Child (24.04.2004, 25 мин.)
30. Assault on South Headquarters (01.05.2004, 25 мин.)
31. Sin (08.05.2004, 25 мин.)
32. Dante of the Deep Forest (15.05.2004, 25 мин.)
33. Al, Captured (29.05.2004, 25 мин.)
34. Theory of Avarice (05.06.2004, 25 мин.)
35. Reunion of the Fallen (12.06.2004, 25 мин.)
36. The Sinner Within (19.06.2004, 25 мин.)
37. The Flame Alchemist, the Bachelor Lieutenant and the Mystery of Warehouse 13 (26.06.2004, 25 мин.)
38. With the River`s Flow (03.07.2004, 25 мин.)
39. Secret of Ishbal (10.07.2004, 25 мин.)
40. The Scar (17.07.2004, 25 мин.)
41. Holy Mother (24.07.2004, 25 мин.)
42. His Name is Unknown (24.07.2004, 25 мин.)
43. The Stray Dog (31.07.2004, 25 мин.)
44. Hohenheim of Light (07.08.2004, 25 мин.)
45. A Rotted Heart (21.08.2004, 25 мин.)
46. Human Transmutation (28.08.2004, 25 мин.)
47. Sealing the Homunculus (04.09.2004, 25 мин.)
48. Goodbye (11.09.2004, 25 мин.)
49. The Other Side of the Gate (18.09.2004, 25 мин.)
50. Death (25.09.2004, 25 мин.)
51. Laws and Promises (02.10.2004, 25 мин.)'
            )
            ->setEpisodesNumber('51')
            ->setFileInfo('+ спэшл')
            ->setName('Fullmetal Alchemist')
            ->setPath($storage->getPath().'Fullmetal Alchemist (2003) [TV]'.DIRECTORY_SEPARATOR)
            ->setStorage($storage)
            ->setSummary(
                'Они нарушили основной закон алхимии и жестоко за это поплатились. И теперь два брата странствуют по '
                .'миру в поисках загадочного философского камня, который поможет им исправить содеянное… Это мир, в '
                .'котором вместо науки властвует магия, в котором люди способны управлять стихиями. Но у магии тоже '
                .'есть законы, которым нужно следовать. В противном случае расплата будет жестокой и страшной. Два '
                .'брата - Эдвард и Альфонс Элрики - пытаются совершить запретное: воскресить умершую мать. Однако '
                .'закон равноценного обмена гласит: чтобы что-то получить, ты должен отдать нечто равноценное…'
            )
            ->setStudio($this->getStudio('Bones'))
            ->setType($this->getType('tv'))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Shounen'))
            ->addGenre($this->getGenre('Fantasy'))
            ->addName((new Name())->setName('Стальной алхимик'))
            ->addName((new Name())->setName('Hagane no Renkin Jutsushi'))
            ->addName((new Name())->setName('Hagane no Renkinjutsushi'))
            ->addName((new Name())->setName('Full Metal Alchemist'))
            ->addName((new Name())->setName('Hagaren'))
            ->addName((new Name())->setName('鋼の錬金術師'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=2960'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=979'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/134/time'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=241943'))
            ->addSource((new Source())->setUrl('http://www1.vecceed.ne.jp/~m-satomi/FULLMETALALCHEMIST.html'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Fullmetal_Alchemist'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/Fullmetal_Alchemist'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E9%8B%BC%E3%81%AE%E9%8C%AC%E9%87%91%E8%A1%93%E5%B8%AB_%28%E3%82%A2%E3%83%8B%E3%83%A1%29'))
            ->addSource((new Source())->setUrl('http://oboi.kards.ru/?act=search&level=6&search_str=FullMetal%20Alchemist'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=124'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=2368'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/121-fullmetal-alchemist'));
    }

    /**
     * Get Spirited Away item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected function getItemSpiritedAway(Storage $storage)
    {
        return (new Item())
            ->setCountry($this->getCountry('JP'))
            ->setCover('example/spirited-away.jpg')
            ->setDatePremiere(new \DateTime('2001-07-20'))
            ->setDuration(125)
            ->setEpisodesNumber('1')
            ->setName('Spirited Away')
            ->setPath($storage->getPath().'Spirited Away (2001)'.DIRECTORY_SEPARATOR)
            ->setStorage($storage)
            ->setSummary(
                'Маленькая Тихиро вместе с мамой и папой переезжают в новый дом. Заблудившись по дороге, они '
                .'оказываются в странном пустынном городе, где их ждет великолепный пир. Родители с жадностью '
                .'набрасываются на еду и к ужасу девочки превращаются в свиней, став пленниками злой колдуньи Юбабы, '
                .'властительницы таинственного мира древних богов и могущественных духов. Теперь, оказавшись одна '
                .'среди магических существ и загадочных видений, отважная Тихиро должна придумать, как избавить своих '
                .'родителей от чар коварной старухи и спастись из пугающего царства призраков...'
            )
            ->setStudio($this->getStudio('Studio Ghibli'))
            ->setType($this->getType('feature'))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Fable'))
            ->addName((new Name())->setName('Унесённые призраками'))
            ->addName((new Name())->setName('Sen to Chihiro no Kamikakushi'))
            ->addName((new Name())->setName('千と千尋の神隠し'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=377'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=112'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=163027'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Spirited_Away'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/%D0%A3%D0%BD%D0%B5%D1%81%D1%91%D0%BD%D0%BD%D1%8B%D0%B5_%D0%BF%D1%80%D0%B8%D0%B7%D1%80%D0%B0%D0%BA%D0%B0%D0%BC%D0%B8'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E5%8D%83%E3%81%A8%E5%8D%83%E5%B0%8B%E3%81%AE%E7%A5%9E%E9%9A%A0%E3%81%97'))
            ->addSource((new Source())->setUrl('http://oboi.kards.ru/?act=search&level=6&search_str=Spirited%20Away'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=368'))
            ->addSource((new Source())->setUrl('http://uanime.org.ua/anime/38.html'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=87'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/199-sen-to-chihiro-no-kamikakushi'));
    }
}
