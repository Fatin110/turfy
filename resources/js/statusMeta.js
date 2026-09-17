const TOURNAMENT_STATUS = {
    draft: { label: 'Draft', variant: 'gray' },
    registration_open: { label: 'Registration Open', variant: 'green' },
    upcoming: { label: 'Upcoming', variant: 'blue' },
    live: { label: 'Live', variant: 'red' },
    completed: { label: 'Completed', variant: 'gray' },
    cancelled: { label: 'Cancelled', variant: 'gray' },
};

const LEAGUE_STATUS = {
    draft: { label: 'Draft', variant: 'gray' },
    upcoming: { label: 'Upcoming', variant: 'blue' },
    ongoing: { label: 'Ongoing', variant: 'red' },
    completed: { label: 'Completed', variant: 'gray' },
};

const REGISTRATION_STATUS = {
    pending_payment: { label: 'Pending Payment', variant: 'amber' },
    confirmed: { label: 'Confirmed', variant: 'green' },
    waitlisted: { label: 'Waitlisted', variant: 'blue' },
    rejected: { label: 'Rejected', variant: 'red' },
    cancelled: { label: 'Cancelled', variant: 'gray' },
};

const MATCH_STATUS = {
    scheduled: { label: 'Scheduled', variant: 'blue' },
    live: { label: 'Live', variant: 'red' },
    completed: { label: 'Completed', variant: 'gray' },
    walkover: { label: 'Walkover', variant: 'amber' },
    cancelled: { label: 'Cancelled', variant: 'gray' },
};

const PAYMENT_STATUS = {
    pending: { label: 'Pending', variant: 'amber' },
    completed: { label: 'Completed', variant: 'green' },
    failed: { label: 'Failed', variant: 'red' },
    refunded: { label: 'Refunded', variant: 'gray' },
};

const VENUE_STATUS = {
    pending: { label: 'Pending Review', variant: 'amber' },
    approved: { label: 'Approved', variant: 'green' },
    rejected: { label: 'Rejected', variant: 'red' },
};

function lookup(map, key) {
    return map[key] ?? { label: key, variant: 'gray' };
}

export const tournamentStatus = (key) => lookup(TOURNAMENT_STATUS, key);
export const leagueStatus = (key) => lookup(LEAGUE_STATUS, key);
export const registrationStatus = (key) => lookup(REGISTRATION_STATUS, key);
export const matchStatus = (key) => lookup(MATCH_STATUS, key);
export const paymentStatus = (key) => lookup(PAYMENT_STATUS, key);
export const venueStatus = (key) => lookup(VENUE_STATUS, key);

export const sportEmoji = (sport) => (sport === 'cricket' ? '🏏' : '⚽');
