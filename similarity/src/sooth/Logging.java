package sooth;

import java.util.logging.ConsoleHandler;
import java.util.logging.Handler;
import java.util.logging.LogRecord;
import java.util.logging.Logger;

/**
 * Provides functions that supply a logger considered aesthetically pleasing by the original developer.
 */
public class Logging {
    /**
     * Formats the log output in a way Petr Hudeček finds the most aesthetically pleasing.
     */
    private static class SimpleRecordFormatter extends java.util.logging.Formatter {

        @Override
        public String format(LogRecord record) {
            return String.valueOf(record.getLevel()) + ": " + formatMessage(record) + " [" + record.getSourceMethodName() + "] " + "\n";
        }
    }

    /**
     * Returns a logger instance with an aesthetically pleasing formatter.
     * @param className The logger name.
     * @return The logger.
     */
    public static Logger getLogger(String className) {
        Logger logger = Logger.getLogger(className);
        for (Handler parentHandler : logger.getParent().getHandlers())
        {
            logger.getParent().removeHandler(parentHandler);
        }
        ConsoleHandler consoleHandler = new ConsoleHandler();
        SimpleRecordFormatter formatter = new SimpleRecordFormatter();
        consoleHandler.setFormatter(formatter);
        logger.addHandler(consoleHandler);
        return logger;
    }
}
