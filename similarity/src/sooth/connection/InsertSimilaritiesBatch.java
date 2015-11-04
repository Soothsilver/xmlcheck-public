package sooth.connection;

import org.jooq.DSLContext;
import org.jooq.InsertValuesStep5;
import sooth.Logging;
import sooth.entities.Tables;
import sooth.entities.tables.records.SimilaritiesRecord;
import sooth.objects.Similarity;

import java.util.Date;
import java.util.logging.Logger;

/**
 * Aggregates many queries inserting new similarity records into the database. When the batch is full, all queries are sent as a single MySQL query.
 * This is done because one query (even a very large one) executes much faster than a large number of smaller queries.
 *
 * However, MySQL servers impose a limit on the maximum length of the query. To prevent this class for causing any MySQL errors,
 * you must increase the value of max_allowed_packet in the MySQL configuration file my.ini. The value "512M" is sufficient.
 */
public class InsertSimilaritiesBatch {
    private final Logger logger = Logging.getLogger(InsertSimilaritiesBatch.class.getName());
    private InsertValuesStep5<SimilaritiesRecord, Integer, Integer, Integer, String, Byte> insertQuery;
    private int boundQueries = 0;
    /**
     * The number of queries that are to be aggregated into a single query.
     * The number 20000 may not be optimal, but it is definitely better than 1 (too many connections to database) and infinity (running out of memory)
     */
    public static final int batchSize = 20000;

    /**
     * Adds to the forming query information about the specified similarity record.
     * @param similarity The similarity record to insert into database.
     */
    public void add(Similarity similarity) {
        if (insertQuery == null) {
            DSLContext context = Database.getContext();
            insertQuery = context.insertInto(Tables.SIMILARITIES, Tables.SIMILARITIES.OLDSUBMISSIONID, Tables.SIMILARITIES.NEWSUBMISSIONID, Tables.SIMILARITIES.SCORE, Tables.SIMILARITIES.DETAILS, Tables.SIMILARITIES.SUSPICIOUS).values(similarity.getOldSubmissionId(), similarity.getNewSubmissionId(), similarity.getScore(), similarity.getDetails(), similarity.isSuspicious() ? new Byte((byte)1) : new Byte((byte)0));
        }
        else {
            insertQuery = insertQuery.values(similarity.getOldSubmissionId(), similarity.getNewSubmissionId(), similarity.getScore(), similarity.getDetails(), similarity.isSuspicious() ? new Byte((byte)1) : new Byte((byte)0));
        }
        boundQueries++;
        if (boundQueries == batchSize) {
            logger.info("Too many insert commands in the batch. Executing...");
            logger.info("Time: " + new Date());
            execute();
            logger.fine("Executed.");
            logger.info("Time: " + new Date());
        }
    }

    /**
     * Executes a single MySQL query that inserts the entire batch into database, then clears the batch.
     */
    public void execute() {
        if (boundQueries == 0) {
            logger.info("There are no queries waiting to be inserted.");
            return;
        }
        insertQuery.execute();
        boundQueries = 0;
        insertQuery = null;
    }
}
