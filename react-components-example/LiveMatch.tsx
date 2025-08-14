// Core Betting Component Example - LiveMatch.tsx
import React, { useState, useEffect, memo } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { motion, AnimatePresence } from 'framer-motion';
import { addToBetSlip } from '../store/slices/bettingSlice';

interface LiveMatchProps {
  match: {
    id: string;
    homeTeam: string;
    awayTeam: string;
    score: { home: number; away: number };
    time: string;
    odds: {
      home: number;
      draw: number;
      away: number;
    };
    isLive: boolean;
  };
}

const LiveMatch: React.FC<LiveMatchProps> = memo(({ match }) => {
  const dispatch = useDispatch();
  const [oddsChanged, setOddsChanged] = useState<string | null>(null);
  
  // Real-time odds animation on change
  useEffect(() => {
    setOddsChanged('all');
    const timer = setTimeout(() => setOddsChanged(null), 1000);
    return () => clearTimeout(timer);
  }, [match.odds]);

  const handleOddsClick = (selection: string, odds: number) => {
    dispatch(addToBetSlip({
      matchId: match.id,
      selection,
      odds,
      match: `${match.homeTeam} vs ${match.awayTeam}`
    }));
  };

  return (
    <motion.div
      className="bg-white rounded-lg shadow-md p-4 mb-4"
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      whileHover={{ scale: 1.02 }}
    >
      {/* Live Indicator */}
      {match.isLive && (
        <div className="flex items-center mb-2">
          <motion.div
            className="w-2 h-2 bg-red-500 rounded-full mr-2"
            animate={{ opacity: [1, 0.3, 1] }}
            transition={{ duration: 1, repeat: Infinity }}
          />
          <span className="text-red-500 text-sm font-medium">
            LIVE {match.time}
          </span>
        </div>
      )}

      {/* Teams & Score */}
      <div className="grid grid-cols-5 items-center mb-4">
        <div className="col-span-2">
          <h3 className="font-semibold">{match.homeTeam}</h3>
        </div>
        <div className="text-center">
          <div className="text-2xl font-bold">
            {match.score.home} - {match.score.away}
          </div>
        </div>
        <div className="col-span-2 text-right">
          <h3 className="font-semibold">{match.awayTeam}</h3>
        </div>
      </div>

      {/* Odds Buttons */}
      <div className="grid grid-cols-3 gap-2">
        <OddsButton
          label="Home"
          odds={match.odds.home}
          isHighlighted={oddsChanged === 'all'}
          onClick={() => handleOddsClick('Home Win', match.odds.home)}
        />
        <OddsButton
          label="Draw"
          odds={match.odds.draw}
          isHighlighted={oddsChanged === 'all'}
          onClick={() => handleOddsClick('Draw', match.odds.draw)}
        />
        <OddsButton
          label="Away"
          odds={match.odds.away}
          isHighlighted={oddsChanged === 'all'}
          onClick={() => handleOddsClick('Away Win', match.odds.away)}
        />
      </div>
    </motion.div>
  );
});

// Optimized Odds Button Component
const OddsButton: React.FC<{
  label: string;
  odds: number;
  isHighlighted: boolean;
  onClick: () => void;
}> = memo(({ label, odds, isHighlighted, onClick }) => {
  return (
    <motion.button
      className={`
        p-3 rounded-lg border-2 transition-all duration-200
        ${isHighlighted 
          ? 'border-green-400 bg-green-50 shadow-lg' 
          : 'border-gray-200 hover:border-blue-400 hover:bg-blue-50'
        }
      `}
      whileTap={{ scale: 0.95 }}
      onClick={onClick}
    >
      <div className="text-xs text-gray-600 mb-1">{label}</div>
      <motion.div 
        className="text-lg font-bold text-blue-600"
        animate={isHighlighted ? { scale: [1, 1.1, 1] } : {}}
      >
        {odds.toFixed(2)}
      </motion.div>
    </motion.button>
  );
});

export default LiveMatch;
