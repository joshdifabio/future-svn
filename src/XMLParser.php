<?php
namespace FutureSVN;

class XMLParser
{
    public static function parseInfoForCommit(\SimpleXMLElement $info)
    {
        foreach ($info->xpath('entry') as $entryXml) {
            if (null !== $entryXml->commit) {
                return self::parseCommit($entryXml->commit);
            }
            
            break;
        }
    }
    
    public static function parseCommit(\SimpleXMLElement $commit)
    {
        return array(
            'revision' => (int)(string)$commit->attributes()->revision,
            'author' => (null === $commit->author ? null : (string)$commit->author),
            'date' => (null === $commit->date ? null : new \DateTime((string)$commit->date))
        );
    }
}
