# Smak 2.00-dev

## Portfolio

This application contains the engine which makes live my photography portfolio. It mainly uses the Finder component of Symfony2 as tree explorer dedicated to photography.

### Usage
    
    // Creates the root collection
    $collection = new \Smak\Portfolio\Collection(__DIR__);

    // Counts the number of sets inside
    echo $collection->count();

    // Gets sets (returns an ArrayIterator of \Smak\Portfolio\Set)
    $sets = $collection->getSets();

    // Gets a specific set
    $my_set = $collection->getSet('set_name');

    // Counts the number of photos in the set
    echo $set->count();

    // Gets set photos (returns an ArrayIterator of \Smak\Portfolio\Photo)
    $photos = $set->getAll();

    // Gets last photo
    $last_photo = $set->getLast();

    // Gets the set template file (\SplFileInfo)
    $template = $set->getTemplate();

    // If the set does not contains any photos but is a sub colllection
    $sub_collection = $set->asCollection()->getSets();

    // If the root collection contains photos, handle it as a Set, get its first photo HTML width and height properties
    $root_set = $collection->asSet()->getByName('my_photo')->getHtmlAttr();

Collection, Set extends the Symfony Finder so you could use all its method for speficic queries.
You have a lot of differents helpers, don't hesitate to read the comments in the code.
