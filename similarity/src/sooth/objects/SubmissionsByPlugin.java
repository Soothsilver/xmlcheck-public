package sooth.objects;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * This class is a data structure containing submissions downloaded from the database.
 *
 * It is, basically, a typedef for HashMap<String, ArrayList<Submission>> where the String is a unique identifier
 * for a plugin and the ArrayList<Submission> is a list sorted by time of submission.
 *
 * Thus, this data structure is basically a list sorted first by plugin and secondly by time of submission.
 *
 * This is useful because we only want to compare submissions with older submission of the same plugin, not with newer
 * submissions and not with submissions verified by another plugin.
 */
public class SubmissionsByPlugin extends HashMap<String, ArrayList<Submission>> {

}
